/* ============================================================
   BAIGR — the signature concentric arcs.
   Half-ring arcs stacked like the BAIGR mark pulse outward in
   sequence — a ripple of growth — while small nodes ride across
   them. No particles. Reacts to the cursor (parallax) and to
   device tilt on mobile.

   ES module: Three.js is resolved through the import map in
   index.html and loaded during idle time so first paint stays fast.
   ============================================================ */

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

  const frameInterval = softwareGL ? 1000 / 30 : 0;

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

  /* ---------- Pointer parallax ---------- */
  const target = { x: 0, y: 0 };

  const onPointer = (e) => {
    target.x = (e.clientX / window.innerWidth) * 2 - 1;
    target.y = -(e.clientY / window.innerHeight) * 2 + 1;
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
    elapsed = 12; // a pleasant static frame
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
