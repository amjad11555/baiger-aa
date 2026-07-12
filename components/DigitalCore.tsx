"use client";

import { useEffect, useRef } from "react";

/**
 * The BAIGR Digital Core — the site's visual signature.
 *
 * An abstract signal organism rendered on 2D canvas:
 *   - a breathing arc core (echo of the logo mark)
 *   - seven orbital paths, one per service, each with a traveling node
 *   - expanding signal rings emitted at random 3–8s intervals
 *   - an intelligent particle field with faint proximity connections
 *   - a gentle magnetic cursor field (150px radius)
 *
 * Adaptive: 120 particles desktop / 60 tablet / 25 mobile.
 * Sleeps when offscreen. Renders a single calm frame under
 * prefers-reduced-motion. No WebGL required — 60fps on canvas.
 */

const LIME = "215, 255, 47";
const PURPLE = "114, 92, 255";
const IVORY = "246, 244, 238";

interface Particle {
  x: number;
  y: number;
  vx: number;
  vy: number;
  r: number;
  seed: number;
  hue: "lime" | "ivory" | "purple";
}

interface Ring {
  r: number;
  alpha: number;
  speed: number;
}

export default function DigitalCore({ className }: { className?: string }) {
  const canvasRef = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;
    const ctx = canvas.getContext("2d");
    if (!ctx) return;

    const reduced = window.matchMedia(
      "(prefers-reduced-motion: reduce)"
    ).matches;

    let width = 0;
    let height = 0;
    let dpr = 1;
    let raf = 0;
    let running = true;
    let visible = true;
    let t = 0;

    let particles: Particle[] = [];
    const rings: Ring[] = [];
    let nextRingAt = 1.2;

    const mouse = { x: -9999, y: -9999 };

    const particleCount = () =>
      window.innerWidth < 768 ? 25 : window.innerWidth < 1200 ? 60 : 120;

    const resize = () => {
      const rect = canvas.getBoundingClientRect();
      dpr = Math.min(window.devicePixelRatio || 1, 2);
      width = rect.width;
      height = rect.height;
      canvas.width = Math.round(width * dpr);
      canvas.height = Math.round(height * dpr);
      ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      seedParticles();
    };

    const seedParticles = () => {
      const n = particleCount();
      particles = Array.from({ length: n }, () => {
        const roll = Math.random();
        return {
          x: Math.random() * width,
          y: Math.random() * height,
          vx: 0,
          vy: 0,
          r: 0.6 + Math.random() * 1.4,
          seed: Math.random() * 1000,
          hue: roll < 0.12 ? "lime" : roll < 0.16 ? "purple" : "ivory",
        } as Particle;
      });
    };

    const colorOf = (p: Particle, alpha: number) => {
      const rgb = p.hue === "lime" ? LIME : p.hue === "purple" ? PURPLE : IVORY;
      return `rgba(${rgb}, ${alpha})`;
    };

    // ---- core arcs (logo echo) ----
    const drawCore = (cx: number, cy: number, scale: number) => {
      const breathe = 1 + Math.sin(t * 0.6) * 0.02;
      const radii = [16, 34, 52, 70].map((r) => r * scale * breathe);
      const a1 = (-160 * Math.PI) / 180;
      const a2 = (-58 * Math.PI) / 180;
      ctx.lineCap = "round";
      radii.forEach((r, i) => {
        ctx.beginPath();
        ctx.arc(cx, cy + 46 * scale, r, a1, a2);
        ctx.strokeStyle = `rgba(${LIME}, ${0.3 - i * 0.04})`;
        ctx.lineWidth = 6 * scale;
        ctx.stroke();
      });
    };

    // ---- seven service orbits ----
    const drawOrbits = (cx: number, cy: number, base: number) => {
      for (let i = 0; i < 7; i++) {
        const rx = base * (0.55 + i * 0.16);
        const ry = rx * 0.42;
        const tilt = -0.28 + i * 0.015;
        const speed = 0.05 + i * 0.017;
        const phase = t * speed + i * 1.7;

        // path
        ctx.beginPath();
        ctx.ellipse(cx, cy, rx, ry, tilt, 0, Math.PI * 2);
        ctx.strokeStyle = `rgba(${IVORY}, ${0.05 - i * 0.004})`;
        ctx.lineWidth = 1;
        ctx.stroke();

        // traveling node
        const nx = Math.cos(phase) * rx;
        const ny = Math.sin(phase) * ry;
        const px = cx + nx * Math.cos(tilt) - ny * Math.sin(tilt);
        const py = cy + nx * Math.sin(tilt) + ny * Math.cos(tilt);
        const nodeColor = i === 3 ? PURPLE : LIME;
        ctx.beginPath();
        ctx.arc(px, py, 2.2, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(${nodeColor}, 0.8)`;
        ctx.fill();
        ctx.beginPath();
        ctx.arc(px, py, 6, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(${nodeColor}, 0.12)`;
        ctx.fill();
      }
    };

    // ---- expanding signal rings ----
    const drawRings = (cx: number, cy: number, dt: number) => {
      if (t > nextRingAt) {
        rings.push({ r: 20, alpha: 0.35, speed: 60 + Math.random() * 40 });
        nextRingAt = t + 3 + Math.random() * 5;
      }
      for (let i = rings.length - 1; i >= 0; i--) {
        const ring = rings[i];
        ring.r += ring.speed * dt;
        ring.alpha *= 0.992;
        if (ring.alpha < 0.01) {
          rings.splice(i, 1);
          continue;
        }
        ctx.beginPath();
        ctx.arc(cx, cy, ring.r, 0, Math.PI * 2);
        ctx.strokeStyle = `rgba(${LIME}, ${ring.alpha})`;
        ctx.lineWidth = 1;
        ctx.stroke();
      }
    };

    // ---- particle field ----
    const drawParticles = (dt: number) => {
      const linkDist = Math.min(120, width * 0.09);
      for (const p of particles) {
        // organic drift
        const drift = 6;
        p.vx += Math.cos(t * 0.3 + p.seed) * drift * dt * 0.06;
        p.vy += Math.sin(t * 0.24 + p.seed * 1.3) * drift * dt * 0.06;

        // cursor field — gentle repulsion within 150px
        const dx = p.x - mouse.x;
        const dy = p.y - mouse.y;
        const d2 = dx * dx + dy * dy;
        if (d2 < 150 * 150 && d2 > 0.01) {
          const d = Math.sqrt(d2);
          const force = ((150 - d) / 150) * 18 * dt;
          p.vx += (dx / d) * force;
          p.vy += (dy / d) * force;
        }

        p.vx *= 0.96;
        p.vy *= 0.96;
        p.x += p.vx + Math.cos(t * 0.12 + p.seed) * 0.12;
        p.y += p.vy + Math.sin(t * 0.1 + p.seed * 0.7) * 0.12;

        if (p.x < -10) p.x = width + 10;
        if (p.x > width + 10) p.x = -10;
        if (p.y < -10) p.y = height + 10;
        if (p.y > height + 10) p.y = -10;

        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = colorOf(p, p.hue === "ivory" ? 0.35 : 0.55);
        ctx.fill();
      }

      // faint connections (only sample neighbours for performance)
      ctx.lineWidth = 0.5;
      for (let i = 0; i < particles.length; i++) {
        for (let j = i + 1; j < Math.min(i + 6, particles.length); j++) {
          const a = particles[i];
          const b = particles[j];
          const dx = a.x - b.x;
          const dy = a.y - b.y;
          const d2 = dx * dx + dy * dy;
          if (d2 < linkDist * linkDist) {
            const alpha = 0.12 * (1 - Math.sqrt(d2) / linkDist);
            ctx.beginPath();
            ctx.moveTo(a.x, a.y);
            ctx.lineTo(b.x, b.y);
            ctx.strokeStyle = `rgba(${IVORY}, ${alpha})`;
            ctx.stroke();
          }
        }
      }
    };

    let last = performance.now();

    const frame = (now: number) => {
      if (!running) return;
      const dt = Math.min((now - last) / 1000, 0.05);
      last = now;
      t += dt;

      ctx.clearRect(0, 0, width, height);

      const cx = width / 2;
      const cy = height / 2;
      const base = Math.min(width, height) * 0.34;
      const coreScale = Math.min(width, height) / 620;

      drawOrbits(cx, cy, base);
      drawRings(cx, cy, dt);
      drawParticles(dt);
      drawCore(cx, cy, Math.max(coreScale, 0.5));

      if (visible && !reduced) raf = requestAnimationFrame(frame);
    };

    const onMouse = (e: MouseEvent) => {
      const rect = canvas.getBoundingClientRect();
      mouse.x = e.clientX - rect.left;
      mouse.y = e.clientY - rect.top;
    };

    const onLeave = () => {
      mouse.x = -9999;
      mouse.y = -9999;
    };

    resize();

    if (reduced) {
      // one calm static frame
      frame(performance.now());
    } else {
      raf = requestAnimationFrame(frame);
      window.addEventListener("mousemove", onMouse, { passive: true });
      canvas.addEventListener("mouseleave", onLeave);
    }

    const ro = new ResizeObserver(resize);
    ro.observe(canvas);

    const io = new IntersectionObserver(
      ([entry]) => {
        visible = entry.isIntersecting;
        if (visible && !reduced) {
          last = performance.now();
          cancelAnimationFrame(raf);
          raf = requestAnimationFrame(frame);
        }
      },
      { threshold: 0.05 }
    );
    io.observe(canvas);

    return () => {
      running = false;
      cancelAnimationFrame(raf);
      ro.disconnect();
      io.disconnect();
      window.removeEventListener("mousemove", onMouse);
      canvas.removeEventListener("mouseleave", onLeave);
    };
  }, []);

  return (
    <canvas
      ref={canvasRef}
      className={className}
      aria-hidden="true"
    />
  );
}
