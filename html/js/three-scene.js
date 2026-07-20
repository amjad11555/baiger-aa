/* ============================================================
   BAIGR — the signature "living silk".
   A slow, flowing liquid-gradient field: domain-warped noise
   ribbons in the brand's lime→iris palette drift like silk over
   the paper page. Calm, premium, deliberately un-busy — the
   colour concentrates on the right so the headline stays crisp.
   Reacts to the cursor with a soft warp and glow.

   ES module: Three.js is resolved through the import map in
   index.html and loaded during idle time so first paint stays fast.
   ============================================================ */

const VERTEX = /* glsl */ `
  varying vec2 vUv;
  void main() {
    vUv = uv;
    gl_Position = vec4(position.xy, 0.0, 1.0);
  }
`;

const FRAGMENT = /* glsl */ `
  precision highp float;
  varying vec2 vUv;

  uniform float uTime;
  uniform float uAspect;
  uniform vec2  uPointer;   // 0..1, page space
  uniform float uPointerOn;
  uniform vec3  uPaper;
  uniform vec3  uLime;
  uniform vec3  uIris;
  uniform vec3  uDeep;
  uniform float uRtl;       // 1.0 mirrors the colour bias for Arabic

  // -- smooth value noise + fbm --------------------------------
  float hash(vec2 p) {
    p = fract(p * vec2(123.34, 345.45));
    p += dot(p, p + 34.345);
    return fract(p.x * p.y);
  }
  float noise(vec2 p) {
    vec2 i = floor(p), f = fract(p);
    float a = hash(i), b = hash(i + vec2(1.0, 0.0));
    float c = hash(i + vec2(0.0, 1.0)), d = hash(i + vec2(1.0, 1.0));
    vec2 u = f * f * (3.0 - 2.0 * f);
    return mix(mix(a, b, u.x), mix(c, d, u.x), u.y);
  }
  float fbm(vec2 p) {
    float v = 0.0, a = 0.5;
    for (int i = 0; i < 5; i++) { v += a * noise(p); p *= 2.0; a *= 0.5; }
    return v;
  }

  void main() {
    vec2 uv = vUv;
    // Aspect-correct sample space so flows aren't stretched.
    vec2 p = vec2(uv.x * uAspect, uv.y) * 2.2;
    float t = uTime * 0.05;

    // Soft warp toward the cursor.
    vec2 warp = (uv - uPointer) * uPointerOn;
    p -= warp * 0.6;

    // Domain warping — the classic marble/silk flow.
    vec2 q = vec2(fbm(p + vec2(0.0, t)), fbm(p + vec2(5.2, -t)));
    vec2 r = vec2(
      fbm(p + 2.0 * q + vec2(1.7, 9.2) + t * 1.1),
      fbm(p + 2.0 * q + vec2(8.3, 2.8) - t * 0.8)
    );
    float f = fbm(p + 2.4 * r);

    // Build the silk colour from the flow.
    vec3 flow = uPaper;
    flow = mix(flow, uLime, smoothstep(0.30, 0.78, f));
    flow = mix(flow, uIris, smoothstep(0.45, 0.92, r.x * 0.5 + 0.5));
    flow = mix(flow, uDeep, smoothstep(0.62, 1.0, f * f) * 0.7);

    // Silky sheen — thin flowing highlights.
    float sheen = 0.5 + 0.5 * sin((f + r.y) * 6.2831 * 1.6 + uTime * 0.25);
    flow += (uIris - uPaper) * 0.05 * sheen;

    // Cursor glow.
    float pd = distance(uv, uPointer);
    float glow = smoothstep(0.4, 0.0, pd) * uPointerOn;
    flow = mix(flow, uLime, glow * 0.18);

    // Bias colour to the "content-free" side; keep the headline calm.
    float side = mix(uv.x, 1.0 - uv.x, uRtl);        // strong side
    float bias = smoothstep(0.15, 0.95, side);
    // Fade to nothing near the very bottom so the section edge is clean.
    float vFade = smoothstep(0.0, 0.25, uv.y) * (1.0 - smoothstep(0.86, 1.0, uv.y) * 0.5);

    // Only the coloured part of the flow becomes visible (paper shows through).
    float intensity = smoothstep(0.34, 0.85, f) + smoothstep(0.5, 1.0, r.x * 0.5 + 0.5) * 0.5;
    float alpha = clamp(intensity, 0.0, 1.0) * bias * vFade * 0.62 + glow * 0.12;

    gl_FragColor = vec4(flow, clamp(alpha, 0.0, 0.72));
  }
`;

function init(THREE, mount) {
  const reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  const isMobile = window.matchMedia("(max-width: 768px)").matches;
  const cleanupFns = [];

  const scene = new THREE.Scene();
  // Fullscreen quad — no perspective needed for a gradient field.
  const camera = new THREE.Camera();

  let renderer;
  try {
    renderer = new THREE.WebGLRenderer({
      alpha: true,
      antialias: true,
      powerPreference: "high-performance",
    });
  } catch (e) {
    return; // No WebGL: the CSS aurora carries the hero alone.
  }

  const gl = renderer.getContext();
  const debugInfo = gl.getExtension("WEBGL_debug_renderer_info");
  const glRenderer = debugInfo
    ? String(gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL))
    : "";
  const softwareGL = /swiftshader|llvmpipe|software/i.test(glRenderer);

  const dpr = Math.min(window.devicePixelRatio, softwareGL ? 1 : isMobile ? 1.5 : 2);
  renderer.setPixelRatio(dpr);
  renderer.setSize(mount.clientWidth, mount.clientHeight);
  mount.appendChild(renderer.domElement);
  mount.classList.add("is-ready");

  const rtl = document.documentElement.dir === "rtl" ? 1 : 0;
  const uniforms = {
    uTime: { value: 0 },
    uAspect: { value: mount.clientWidth / mount.clientHeight },
    uPointer: { value: new THREE.Vector2(rtl ? 0.28 : 0.72, 0.5) },
    uPointerOn: { value: 0 },
    uPaper: { value: new THREE.Color("#fefefe") },
    uLime: { value: new THREE.Color("#d6e06a") },
    uIris: { value: new THREE.Color("#726bd6") },
    uDeep: { value: new THREE.Color("#4d43b8") },
    uRtl: { value: rtl },
  };

  const material = new THREE.ShaderMaterial({
    vertexShader: VERTEX,
    fragmentShader: FRAGMENT,
    uniforms,
    transparent: true,
    depthWrite: false,
    depthTest: false,
  });
  const quad = new THREE.Mesh(new THREE.PlaneGeometry(2, 2), material);
  scene.add(quad);

  // Keep colour bias mirrored when the language flips.
  const onLang = () => {
    const isRtl = document.documentElement.dir === "rtl" ? 1 : 0;
    uniforms.uRtl.value = isRtl;
  };
  document.addEventListener("baigr:langchange", onLang);
  cleanupFns.push(() => document.removeEventListener("baigr:langchange", onLang));

  /* ---------- Cursor warp ---------- */
  const pointerTarget = new THREE.Vector2(uniforms.uPointer.value.x, uniforms.uPointer.value.y);
  let onTarget = 0;
  const onPointer = (e) => {
    pointerTarget.set(e.clientX / window.innerWidth, 1 - e.clientY / window.innerHeight);
    onTarget = 1;
  };
  window.addEventListener("pointermove", onPointer, { passive: true });
  cleanupFns.push(() => window.removeEventListener("pointermove", onPointer));
  const onLeave = () => (onTarget = 0);
  window.addEventListener("pointerout", onLeave);
  cleanupFns.push(() => window.removeEventListener("pointerout", onLeave));

  /* ---------- Render loop (paused off-screen / hidden tab) ---------- */
  let raf = 0;
  let running = false;
  let last = performance.now();
  let elapsed = 0;
  const frameInterval = softwareGL ? 1000 / 30 : 0;
  let lastFrame = 0;

  const renderFrame = () => {
    const now = performance.now();
    const dt = Math.min((now - last) / 1000, 0.1);
    last = now;
    elapsed += dt;
    uniforms.uTime.value = elapsed;

    // Ease the pointer + its influence for a soft, liquid feel.
    uniforms.uPointer.value.x += (pointerTarget.x - uniforms.uPointer.value.x) * 0.04;
    uniforms.uPointer.value.y += (pointerTarget.y - uniforms.uPointer.value.y) * 0.04;
    uniforms.uPointerOn.value += (onTarget - uniforms.uPointerOn.value) * 0.05;

    renderer.render(scene, camera);
  };

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
    uniforms.uTime.value = 8; // a pleasant static frame
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
    uniforms.uAspect.value = mount.clientWidth / mount.clientHeight;
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
