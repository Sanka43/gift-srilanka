/**
 * GiftSriLanka - Festive Snow Theme & Canvas Snowfall Engine
 * Lightweight, high-performance, 60fps snow animation with subtle physics,
 * retina scaling, reduced CPU usage when hidden, and high-visibility contrast rendering
 * that shines brilliantly on both pure white and dark backgrounds.
 */

export class Snowfall {
    constructor(options = {}) {
        this.options = Object.assign({
            canvasId: 'snowfall-canvas',
            minFlakesMobile: 45,
            maxFlakesMobile: 65,
            minFlakesDesktop: 90,
            maxFlakesDesktop: 140,
            minRadius: 1.5,
            maxRadius: 4.4,
            minSpeed: 0.65,
            maxSpeed: 2.1,
            minWind: -0.6,
            maxWind: 0.6,
            minOpacity: 0.60,
            maxOpacity: 0.98,
            storageKey: 'giftsrilanka_snow_enabled',
        }, options);

        this.canvas = null;
        this.ctx = null;
        this.flakes = [];
        this.animId = null;
        this.lastTime = 0;
        this.width = 0;
        this.height = 0;
        this.dpr = 1;
        this.windAngle = 0;
        this.resizeDebounceTimer = null;

        // Christmas Season: Always enabled across the site
        this.enabled = true;

        this.init();
    }

    init() {
        // Find or create canvas
        this.canvas = document.getElementById(this.options.canvasId);
        if (!this.canvas) {
            this.canvas = document.createElement('canvas');
            this.canvas.id = this.options.canvasId;
            this.canvas.className = 'snowfall-canvas';
            this.canvas.setAttribute('aria-hidden', 'true');
            document.body.appendChild(this.canvas);
        }

        this.ctx = this.canvas.getContext('2d', { alpha: true });
        if (!this.ctx) return;

        // Resize handling
        this.handleResize = () => {
            clearTimeout(this.resizeDebounceTimer);
            this.resizeDebounceTimer = setTimeout(() => this.resize(), 150);
        };
        window.addEventListener('resize', this.handleResize, { passive: true });
        this.resize();

        // Pause animation when page is not visible to conserve battery / GPU
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pause();
            } else if (this.enabled) {
                this.resume();
            }
        });

        // Event listeners for global triggers
        window.addEventListener('snow:toggle', () => this.toggle());
        window.addEventListener('snow:set', (e) => {
            if (e.detail && typeof e.detail.enabled === 'boolean') {
                this.set(e.detail.enabled);
            }
        });

        // Update body / HTML classes
        this.updateThemeClass();

        if (this.enabled) {
            this.start();
        } else {
            this.canvas.style.display = 'none';
        }

        // Attach global instance
        window.GiftSriLankaSnow = this;
    }

    resize() {
        this.width = window.innerWidth;
        this.height = window.innerHeight;
        this.dpr = Math.min(window.devicePixelRatio || 1, 2);

        this.canvas.width = Math.floor(this.width * this.dpr);
        this.canvas.height = Math.floor(this.height * this.dpr);
        this.canvas.style.width = `${this.width}px`;
        this.canvas.style.height = `${this.height}px`;

        if (this.ctx) {
            this.ctx.setTransform(this.dpr, 0, 0, this.dpr, 0, 0);
        }

        const isMobile = this.width < 768;
        const count = isMobile
            ? Math.floor(this.options.minFlakesMobile + Math.random() * (this.options.maxFlakesMobile - this.options.minFlakesMobile))
            : Math.floor(this.options.minFlakesDesktop + Math.random() * (this.options.maxFlakesDesktop - this.options.minFlakesDesktop));

        this.createFlakes(count);
    }

    createFlakes(count) {
        this.flakes = [];
        for (let i = 0; i < count; i++) {
            this.flakes.push(this.createFlake(true));
        }
    }

    createFlake(randomY = false) {
        // ~12% crystalline star flakes for festive elegance
        const isStar = Math.random() < 0.12;
        const radius = this.options.minRadius + Math.random() * (this.options.maxRadius - this.options.minRadius);
        const opacity = this.options.minOpacity + Math.random() * (this.options.maxOpacity - this.options.minOpacity);

        // Multi-tone winter palette for maximum contrast on white backgrounds:
        // 60% Diamond White with cool slate shadow
        // 25% Glacier Sky Blue (pops vividly against white cards)
        // 15% Crystalline Aqua / Silver Sparkle
        const roll = Math.random();
        let fillColor, shadowColor, rimColor;

        if (roll < 0.60) {
            fillColor = `rgba(255, 255, 255, ${opacity})`;
            shadowColor = 'rgba(15, 23, 42, 0.28)';
            rimColor = `rgba(148, 163, 184, ${Math.min(opacity * 0.5, 0.45)})`;
        } else if (roll < 0.85) {
            fillColor = `rgba(186, 230, 253, ${opacity})`; // sky-200
            shadowColor = 'rgba(14, 116, 144, 0.35)'; // cyan-700
            rimColor = `rgba(56, 189, 248, ${Math.min(opacity * 0.6, 0.5)})`;
        } else {
            fillColor = `rgba(224, 242, 254, ${opacity})`; // sky-100
            shadowColor = 'rgba(2, 132, 199, 0.32)'; // sky-600
            rimColor = `rgba(125, 211, 252, ${Math.min(opacity * 0.6, 0.55)})`;
        }

        return {
            x: Math.random() * this.width,
            y: randomY ? Math.random() * this.height : -15,
            radius: radius,
            speed: this.options.minSpeed + (radius / this.options.maxRadius) * (this.options.maxSpeed - this.options.minSpeed) + Math.random() * 0.4,
            wind: this.options.minWind + Math.random() * (this.options.maxWind - this.options.minWind),
            angle: Math.random() * Math.PI * 2,
            angularSpeed: 0.015 + Math.random() * 0.025,
            opacity: opacity,
            isStar: isStar,
            fillColor: fillColor,
            shadowColor: shadowColor,
            rimColor: rimColor,
            rotation: Math.random() * Math.PI * 2,
            rotSpeed: (Math.random() - 0.5) * 0.04,
        };
    }

    updateThemeClass() {
        if (this.enabled) {
            document.documentElement.classList.add('snow-theme-active');
            document.body.classList.add('snow-theme-active');
        } else {
            document.documentElement.classList.remove('snow-theme-active');
            document.body.classList.remove('snow-theme-active');
        }
        window.dispatchEvent(new CustomEvent('snow:changed', { detail: { enabled: this.enabled } }));
    }

    start() {
        if (this.canvas) {
            this.canvas.style.display = 'block';
        }
        if (!this.animId) {
            this.lastTime = performance.now();
            this.animId = requestAnimationFrame((t) => this.loop(t));
        }
    }

    pause() {
        if (this.animId) {
            cancelAnimationFrame(this.animId);
            this.animId = null;
        }
    }

    resume() {
        if (this.enabled && !this.animId) {
            this.lastTime = performance.now();
            this.animId = requestAnimationFrame((t) => this.loop(t));
        }
    }

    stop() {
        this.pause();
        if (this.ctx) {
            this.ctx.clearRect(0, 0, this.width, this.height);
        }
        if (this.canvas) {
            this.canvas.style.display = 'none';
        }
    }

    toggle() {
        this.set(!this.enabled);
    }

    set(val) {
        this.enabled = !!val;
        try {
            localStorage.setItem(this.options.storageKey, this.enabled ? 'true' : 'false');
        } catch (e) {
            // Safe fallback if local storage is disabled
        }
        this.updateThemeClass();
        if (this.enabled) {
            this.start();
        } else {
            this.stop();
        }
    }

    loop(time) {
        if (!this.enabled) return;

        const delta = Math.min((time - this.lastTime) / 1000, 0.1);
        this.lastTime = time;

        this.windAngle += delta * 0.45;
        const naturalWind = Math.sin(this.windAngle) * 0.5;

        this.ctx.clearRect(0, 0, this.width, this.height);

        for (let i = 0; i < this.flakes.length; i++) {
            const flake = this.flakes[i];

            // Gentle natural sway & wind motion
            flake.angle += flake.angularSpeed;
            const sway = Math.sin(flake.angle) * 0.6;
            flake.x += (flake.wind + naturalWind + sway) * (flake.speed * 42 * delta);
            flake.y += flake.speed * 58 * delta;
            flake.rotation += flake.rotSpeed;

            // Recycle flake when out of bounds
            if (flake.y > this.height + 15) {
                this.flakes[i] = this.createFlake(false);
            }
            if (flake.x > this.width + 15) {
                flake.x = -15;
            } else if (flake.x < -15) {
                flake.x = this.width + 15;
            }

            // Render snowflake
            if (flake.isStar) {
                this.drawStarFlake(flake);
            } else {
                this.drawCircleFlake(flake);
            }
        }

        this.animId = requestAnimationFrame((t) => this.loop(t));
    }

    drawCircleFlake(flake) {
        const ctx = this.ctx;
        ctx.save();
        ctx.beginPath();
        ctx.arc(flake.x, flake.y, flake.radius, 0, Math.PI * 2);

        // Contrasting 3D shadow for instant visibility against white backgrounds
        ctx.shadowColor = flake.shadowColor;
        ctx.shadowBlur = flake.radius > 2.5 ? 4 : 2;
        ctx.shadowOffsetX = 0.5;
        ctx.shadowOffsetY = 1;

        ctx.fillStyle = flake.fillColor;
        ctx.fill();

        // Subtle crisp rim for medium/large flakes so edges remain defined on 100% white
        if (flake.radius > 2.0) {
            ctx.shadowColor = 'transparent';
            ctx.strokeStyle = flake.rimColor;
            ctx.lineWidth = 0.5;
            ctx.stroke();
        }

        ctx.restore();
    }

    drawStarFlake(flake) {
        const ctx = this.ctx;
        const r = flake.radius * 1.6;
        ctx.save();
        ctx.translate(flake.x, flake.y);
        ctx.rotate(flake.rotation);

        // Two-pass rendering for maximum contrast on any background:
        // Pass 1: Contrasting deep cool shadow halo
        // Pass 2: Inner crisp bright stroke
        const passes = [
            {
                color: 'rgba(15, 23, 42, 0.32)',
                width: 2.4,
                blur: 2,
            },
            {
                color: flake.fillColor,
                width: 1.2,
                blur: 0,
            }
        ];

        for (let p = 0; p < passes.length; p++) {
            const pass = passes[p];
            ctx.strokeStyle = pass.color;
            ctx.lineWidth = pass.width;
            ctx.shadowColor = pass.blur ? 'rgba(14, 116, 144, 0.4)' : 'transparent';
            ctx.shadowBlur = pass.blur;

            for (let a = 0; a < 3; a++) {
                ctx.beginPath();
                ctx.moveTo(-r, 0);
                ctx.lineTo(r, 0);
                ctx.stroke();

                const br = r * 0.45;
                ctx.beginPath();
                ctx.moveTo(r * 0.6, -br * 0.5);
                ctx.lineTo(r * 0.6, br * 0.5);
                ctx.moveTo(-r * 0.6, -br * 0.5);
                ctx.lineTo(-r * 0.6, br * 0.5);
                ctx.stroke();

                ctx.rotate(Math.PI / 3);
            }
            // Rotate back 3 * (PI/3) = PI to align with initial angle for pass 2
            ctx.rotate(Math.PI);
        }

        ctx.restore();
    }
}

// Auto-initialize when DOM is ready
export function initSnowfall() {
    if (typeof window === 'undefined') return;

    const start = () => {
        if (!window.GiftSriLankaSnow) {
            new Snowfall();
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
}
