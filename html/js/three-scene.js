/* ============================================================
   BAIGR — the signature "growth rise".
   Motes drift steadily upward, fanning out gently as they climb —
   compounding growth drawn in light, no swirl. Behind them,
   concentric arcs pulse outward in sequence, echoing the BAIGR
   mark. Reacts to the cursor (parallax + local glow) and to
   device tilt on mobile.

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
    float speed = 0.05 + aSeed * 0.06;
    float t = fract(aOffset + uTime * speed);

    // Calm rise: motes climb straight up, fanning out gently — no swirl.
    float lane = (aSeed - 0.5) * 2.0;              // -1..1 horizontal lane
    float spread = 1.4 + t * 2.6;                  // widen as they climb (growth)
    float x = lane * spread;
    float y = t * 7.4 - 3.4;
    float z = (fract(aSeed * 7.31) - 0.5) * 2.6;   // fixed depth per mote

    // Gentle sway so the field breathes (no rotation).
    x += sin(uTime * 0.3 + aSeed * 9.0) * 0.18;
    y += sin(uTime * 0.5 + aSeed * 4.0) * 0.08;

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

    // Ramp lime -> iris as the bloom rises; deepen to navy near the cursor.
    vec3 color = mix(uColorA, uColorB, clamp(varT * 0.95, 0.0, 1.0));
    color = mix(color, uColorGlow, varGlow * 0.7);

    // Fade in at the seed, out at the crown of the bloom.
    float life = smoothstep(0.0, 0.09, varT) * (1.0 - smoothstep(0.80, 1.0, varT));
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

  /* ---------- Growth bloom particles ---------- */
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

  /* ---------- Concentric arc system — the BAIGR mark, alive ----------
     Half-ring arcs stacked like the logo's fingerprint, pulsing
     outward in sequence. Small nodes orbit the outer arc. */
  const markGroup = new THREE.Group();
  markGroup.position.set(isMobile ? 0 : 3.3, 1.0, -2.2);
  markGroup.rotation.x = 0.42;
  markGroup.rotation.z = -0.32; // matches the mark's -18° tilt, felt in 3D
  scene.add(markGroup);

  const arcColors = ["#d6e06a", "#aeb93f", "#726bd6", "#4d43b8"];
  const arcs = [];
  for (let i = 0; i < 4; i++) {
    const r = 0.7 + i * 0.72;
    const arcGeo = new THREE.TorusGeometry(r, 0.028, 10, 80, Math.PI);
    const arcMat = new THREE.MeshBasicMaterial({
      color: new THREE.Color(arcColors[i]),
      transparent: true,
      opacity: 0.5 - i * 0.07,
    });
    const arc = new THREE.Mesh(arcGeo, arcMat);
    markGroup.add(arc);
    arcs.push({ mesh: arc, base: 0.5 - i * 0.07, phase: i * 0.7 });
  }

  // Orbiting nodes — glints of momentum riding the arcs.
  const nodes = [];
  const nodeGeo = new THREE.SphereGeometry(0.058, 12, 12);
  for (let i = 0; i < 3; i++) {
    const nodeMat = new THREE.MeshBasicMaterial({
      color: new THREE.Color(i % 2 ? "#726bd6" : "#d6e06a"),
      transparent: true,
      opacity: 0.9,
    });
    const node = new THREE.Mesh(nodeGeo, nodeMat);
    markGroup.add(node);
    nodes.push({
      mesh: node,
      r: 1.5 + i * 0.7,
      phase: i * 2.1,
      speed: 0.45 + i * 0.18,
    });
  }

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

    // The mark drifts and wobbles in depth; each arc pulses in sequence
    // like a ripple radiating from the centre.
    markGroup.position.y = 1.0 + Math.sin(elapsed * 0.5) * 0.22;
    markGroup.rotation.y = Math.sin(elapsed * 0.35) * 0.35;
    arcs.forEach((a) => {
      a.mesh.rotation.z += dt * 0.14;
      a.mesh.material.opacity =
        a.base * (0.55 + 0.45 * Math.sin(elapsed * 1.3 - a.phase));
    });

    // Nodes ride circular paths across the mark's face.
    nodes.forEach((n) => {
      const ang = elapsed * n.speed + n.phase;
      n.mesh.position.set(
        Math.cos(ang) * n.r,
        Math.abs(Math.sin(ang)) * n.r * 0.6,
        Math.sin(ang * 0.7) * 0.3
      );
    });

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
