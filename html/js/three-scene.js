/* ============================================================
   BAIGR — the signature "growth field".
   Thousands of warm ember motes rise along a compounding curve —
   growth drawn in light. Reacts to the cursor (parallax + local
   glow) and to device tilt on mobile.

   ES module: Three.js is resolved through the import map in
   index.html and loaded during idle time so first paint stays fast.
   ============================================================ */

const VERTEX = /* glsl */ `
  attribute float aSeed;
  attribute float aOffset;
  uniform float uTime;
  uniform vec2 uPointer;   // world-space pointer on the z=0 plane
  uniform float uPixelRatio;
  varying float varT;
  varying float varGlow;

  void main() {
    float speed = 0.016 + aSeed * 0.022;
    float t = fract(aOffset + uTime * speed);

    // The growth curve: slow start, compounding rise.
    float x = mix(-7.5, 7.5, t);
    float y = pow(t, 1.55) * 5.2 - 2.4;

    // Helical drift around the path so the stream feels alive.
    float angle = t * 12.0 + aSeed * 6.28318 + uTime * (0.15 + aSeed * 0.2);
    float radius = 0.35 + aSeed * 1.15;
    y += sin(angle) * radius * 0.45;
    float z = cos(angle) * radius - aSeed * 1.5;

    // Gentle breathing across the whole field.
    x += sin(uTime * 0.22 + aSeed * 9.0) * 0.18;

    vec3 pos = vec3(x, y, z);

    // Local glow: particles brighten near the cursor.
    float d = distance(pos.xy, uPointer);
    varGlow = smoothstep(2.6, 0.0, d);
    varT = t;

    vec4 mv = modelViewMatrix * vec4(pos, 1.0);
    gl_Position = projectionMatrix * mv;

    float size = (0.7 + aSeed * 1.9) * (1.0 + varGlow * 1.6);
    gl_PointSize = size * uPixelRatio * (36.0 / -mv.z);
  }
`;

const FRAGMENT = /* glsl */ `
  uniform vec3 uColorA;    // lime
  uniform vec3 uColorB;    // iris
  uniform vec3 uColorGlow; // navy — cursor concentration
  varying float varT;
  varying float varGlow;

  void main() {
    vec2 uv = gl_PointCoord - 0.5;
    float dist = length(uv);
    float disc = smoothstep(0.5, 0.08, dist);
    if (disc < 0.01) discard;

    // Ramp lime -> iris along the growth curve; deepen to navy near the cursor.
    vec3 color = mix(uColorA, uColorB, clamp(varT * 0.9, 0.0, 1.0));
    color = mix(color, uColorGlow, varGlow * 0.7);

    // Fade in at birth, out at the top of the arc.
    float life = smoothstep(0.0, 0.08, varT) * (1.0 - smoothstep(0.82, 1.0, varT));
    // Normal blending over a light page: keep enough alpha to read on paper.
    float alpha = disc * life * (0.62 + varGlow * 0.38);

    gl_FragColor = vec4(color, alpha);
  }
`;

function init(THREE, mount) {
  const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const isMobile = window.matchMedia("(max-width: 768px)").matches;
  const cleanupFns = [];

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(
    50,
    mount.clientWidth / mount.clientHeight,
    0.1,
    50
  );
  camera.position.set(0, 0.4, 8.5);

  let renderer;
  try {
    renderer = new THREE.WebGLRenderer({
      alpha: true,
      antialias: false,
      powerPreference: "high-performance",
    });
  } catch (e) {
    return; // No WebGL: the aurora gradient carries the hero alone.
  }

  // Software rasterizers (SwiftShader, llvmpipe…) render on the CPU:
  // scale the scene down so weak machines stay smooth.
  const gl = renderer.getContext();
  const debugInfo = gl.getExtension("WEBGL_debug_renderer_info");
  const glRenderer = debugInfo
    ? String(gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL))
    : "";
  const softwareGL = /swiftshader|llvmpipe|software/i.test(glRenderer);

  const dpr = Math.min(
    window.devicePixelRatio,
    softwareGL ? 1 : isMobile ? 1.5 : 1.75
  );
  renderer.setPixelRatio(dpr);
  renderer.setSize(mount.clientWidth, mount.clientHeight);
  mount.appendChild(renderer.domElement);
  mount.classList.add("is-ready");

  /* ---------- Growth stream particles ---------- */
  const count = softwareGL ? 700 : isMobile ? 1300 : 2600;
  const frameInterval = softwareGL ? 1000 / 24 : 0;
  const seeds = new Float32Array(count);
  const offsets = new Float32Array(count);
  const positions = new Float32Array(count * 3); // real positions live in the shader
  for (let i = 0; i < count; i++) {
    seeds[i] = Math.random();
    offsets[i] = Math.random();
  }
  const geometry = new THREE.BufferGeometry();
  geometry.setAttribute("position", new THREE.BufferAttribute(positions, 3));
  geometry.setAttribute("aSeed", new THREE.BufferAttribute(seeds, 1));
  geometry.setAttribute("aOffset", new THREE.BufferAttribute(offsets, 1));
  geometry.boundingSphere = new THREE.Sphere(new THREE.Vector3(0, 0, 0), 20);

  const uniforms = {
    uTime: { value: 0 },
    uPointer: { value: new THREE.Vector2(0, 0.6) },
    uPixelRatio: { value: dpr },
    uColorA: { value: new THREE.Color("#d6e06a") }, // lime
    uColorB: { value: new THREE.Color("#726bd6") }, // iris
    uColorGlow: { value: new THREE.Color("#1b1539") }, // navy
  };

  const material = new THREE.ShaderMaterial({
    vertexShader: VERTEX,
    fragmentShader: FRAGMENT,
    uniforms,
    transparent: true,
    depthWrite: false,
    blending: THREE.NormalBlending, // additive is invisible on a light page
  });

  scene.add(new THREE.Points(geometry, material));

  /* ---------- Floating wireframe polyhedra (layered 3D anchor) ---------- */
  const icoGeo = new THREE.IcosahedronGeometry(2.1, 1);
  const icoMat = new THREE.MeshBasicMaterial({
    color: new THREE.Color("#726bd6"),
    wireframe: true,
    transparent: true,
    opacity: 0.2,
  });
  const ico = new THREE.Mesh(icoGeo, icoMat);
  ico.position.set(isMobile ? 0 : 3.4, 1.1, -2.5);
  scene.add(ico);

  // A larger, fainter dodecahedron rotating the other way — adds depth.
  const dodGeo = new THREE.DodecahedronGeometry(3.4, 0);
  const dodMat = new THREE.MeshBasicMaterial({
    color: new THREE.Color("#aeb93f"),
    wireframe: true,
    transparent: true,
    opacity: 0.1,
  });
  const dod = new THREE.Mesh(dodGeo, dodMat);
  dod.position.copy(ico.position);
  scene.add(dod);

  // Halo ring — a thin torus catching the eye behind the marks.
  const ringGeo = new THREE.TorusGeometry(2.85, 0.02, 12, 90);
  const ringMat = new THREE.MeshBasicMaterial({
    color: new THREE.Color("#726bd6"),
    transparent: true,
    opacity: 0.22,
  });
  const ring = new THREE.Mesh(ringGeo, ringMat);
  ring.position.copy(ico.position);
  ring.rotation.x = 1.1;
  scene.add(ring);

  /* ---------- Pointer parallax + interactive light ---------- */
  const target = { x: 0, y: 0 };
  const raycastPlane = new THREE.Plane(new THREE.Vector3(0, 0, 1), 0);
  const raycaster = new THREE.Raycaster();
  const ndc = new THREE.Vector2();
  const hit = new THREE.Vector3();

  const onPointer = (e) => {
    const nx = (e.clientX / window.innerWidth) * 2 - 1;
    const ny = -(e.clientY / window.innerHeight) * 2 + 1;
    target.x = nx;
    target.y = ny;
    ndc.set(nx, ny);
    raycaster.setFromCamera(ndc, camera);
    if (raycaster.ray.intersectPlane(raycastPlane, hit)) {
      uniforms.uPointer.value.set(hit.x, hit.y);
    }
  };
  window.addEventListener("pointermove", onPointer, { passive: true });
  cleanupFns.push(() => window.removeEventListener("pointermove", onPointer));

  // Gyroscope on mobile
  const clamp = (v, min, max) => Math.min(max, Math.max(min, v));
  const onOrientation = (e) => {
    if (e.gamma == null || e.beta == null) return;
    target.x = clamp(e.gamma / 30, -1, 1);
    target.y = clamp((e.beta - 45) / -30, -1, 1);
  };
  if (isMobile) {
    window.addEventListener("deviceorientation", onOrientation);
    cleanupFns.push(() =>
      window.removeEventListener("deviceorientation", onOrientation)
    );
  }

  /* ---------- Render loop (paused off-screen / hidden tab) ---------- */
  let raf = 0;
  let running = false;
  let last = performance.now();
  let elapsed = 0;

  const renderFrame = () => {
    const now = performance.now();
    const dt = Math.min((now - last) / 1000, 0.1);
    last = now;
    elapsed += dt;
    uniforms.uTime.value = elapsed;

    camera.position.x += (target.x * 0.7 - camera.position.x) * 0.045;
    camera.position.y += (0.4 + target.y * 0.45 - camera.position.y) * 0.045;
    camera.lookAt(0, 0.3, 0);

    ico.rotation.x += dt * 0.08;
    ico.rotation.y += dt * 0.11;
    ico.position.y = 1.1 + Math.sin(elapsed * 0.5) * 0.25;

    dod.rotation.x -= dt * 0.05;
    dod.rotation.y -= dt * 0.07;
    dod.position.y = ico.position.y;

    ring.rotation.z += dt * 0.12;
    ring.position.y = ico.position.y;

    renderer.render(scene, camera);
  };

  let lastFrame = 0;
  const loop = (ts) => {
    if (!frameInterval || ts - lastFrame >= frameInterval) {
      lastFrame = ts;
      renderFrame();
    }
    raf = requestAnimationFrame(loop);
  };

  const start = () => {
    if (running || reduced) return;
    running = true;
    last = performance.now();
    raf = requestAnimationFrame(loop);
  };
  const stop = () => {
    running = false;
    cancelAnimationFrame(raf);
  };

  if (reduced) {
    uniforms.uTime.value = 12; // a pleasant static frame
    renderFrame();
  } else {
    const io = new IntersectionObserver(([entry]) =>
      entry.isIntersecting ? start() : stop()
    );
    io.observe(mount);
    const onVis = () => (document.hidden ? stop() : start());
    document.addEventListener("visibilitychange", onVis);
    cleanupFns.push(() => {
      io.disconnect();
      document.removeEventListener("visibilitychange", onVis);
    });
  }

  const onResize = () => {
    camera.aspect = mount.clientWidth / mount.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(mount.clientWidth, mount.clientHeight);
    if (reduced) renderFrame();
  };
  window.addEventListener("resize", onResize);
  cleanupFns.push(() => window.removeEventListener("resize", onResize));
}

/* Boot during idle time so hydration of the page stays snappy. */
const boot = () => {
  const mount = document.getElementById("hero-field");
  if (!mount) return;
  import("three")
    .then((THREE) => init(THREE, mount))
    .catch((err) => console.warn("[BAIGR] three.js unavailable:", err.message));
};

if ("requestIdleCallback" in window) {
  requestIdleCallback(boot, { timeout: 1200 });
} else {
  setTimeout(boot, 350);
}
