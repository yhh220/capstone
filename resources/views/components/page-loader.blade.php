@persist('global-page-loader')
<div x-data="carLoader()" 
     x-init="initLoader()"
     x-show="isLoading"
     x-transition:leave="transition fade-out duration-500 ease-in"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0 backdrop-blur-none"
     style="display: none;"
     class="fixed inset-0 z-[99999] flex flex-col items-center justify-center bg-[#0C0C0E]/95 backdrop-blur-md">
    
    <div class="w-[min(72vmin,240px)] aspect-[1.5] relative grid place-items-center mb-6">
        <svg viewBox="0 0 300 150" fill="none" aria-hidden="true" class="w-full h-full overflow-visible">
            <g x-ref="group">
                <path x-ref="path" 
                      d="M 50 100 
                         C 30 100, 20 90, 20 80 
                         L 20 70 
                         C 20 50, 40 40, 60 40 
                         L 90 35 
                         C 120 20, 150 15, 180 20 
                         L 230 40 
                         C 260 45, 275 55, 280 75 
                         C 285 95, 270 100, 250 100 
                         L 220 100 
                         A 25 25 0 0 0 170 100 
                         L 130 100 
                         A 25 25 0 0 0 80 100 
                         Z" 
                      stroke="currentColor" 
                      stroke-linecap="round" 
                      stroke-linejoin="round" 
                      stroke-width="5"
                      opacity="0.05"
                      class="text-gray-600 dark:text-gray-400">
                </path>
                <!-- Particles will be injected here -->
            </g>
        </svg>
    </div>
    <div class="text-center font-sans tracking-wide">
        <h2 class="text-xl font-bold text-brand-red animate-pulse">Carbon Heat Loading...</h2>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('carLoader', () => ({
            isLoading: false, // Default is false so it doesn't flash instantly
            startedAt: null,
            animationFrame: null,
            particleNodes: [],
            
            // Config matching the design
            particleCount: 50,
            trailSpan: 0.4,
            durationMs: 3000,
            
            normalizeProgress(progress) {
                return ((progress % 1) + 1) % 1;
            },
            
            getPointAt(progress) {
                const path = this.$refs.path;
                if(!path) return {x:0, y:0};
                const pt = path.getPointAtLength(progress * path.getTotalLength());
                return { x: pt.x, y: pt.y };
            },
            
            getParticleData(index, progress) {
                const tailOffset = index / (this.particleCount - 1);
                const point = this.getPointAt(this.normalizeProgress(progress - tailOffset * this.trailSpan));
                const fade = Math.pow(1 - tailOffset, 0.6);
                
                return {
                    x: point.x,
                    y: point.y,
                    radius: 1 + fade * 3.5,
                    opacity: 0.05 + fade * 0.95,
                };
            },
            
            render(now) {
                // Return if stopped
                if(!this.isLoading || !this.$refs.path) return;
                
                if (!this.startedAt) this.startedAt = now;
                const time = now - this.startedAt;
                const progress = (time % this.durationMs) / this.durationMs;
                
                this.particleNodes.forEach((node, index) => {
                    const particle = this.getParticleData(index, progress);
                    node.setAttribute('cx', particle.x.toFixed(2));
                    node.setAttribute('cy', particle.y.toFixed(2));
                    node.setAttribute('r', particle.radius.toFixed(2));
                    node.setAttribute('opacity', particle.opacity.toFixed(3));
                });
                
                this.animationFrame = requestAnimationFrame((n) => this.render(n));
            },
            
            initLoader() {
                // Only inject if not already done
                if(this.particleNodes.length === 0 && this.$refs.group) {
                    const SVG_NS = 'http://www.w3.org/2000/svg';
                    for(let i=0; i<this.particleCount; i++) {
                        const circle = document.createElementNS(SVG_NS, 'circle');
                        circle.setAttribute('fill', '#E11D48');
                        this.$refs.group.appendChild(circle);
                        this.particleNodes.push(circle);
                    }
                }
                
                // Event Listeners for Page and Livewire routing
                window.addEventListener('load', () => this.stopLoader());
                document.addEventListener('livewire:navigate', () => this.startLoader());
                document.addEventListener('livewire:navigated', () => this.stopLoader());
                
                // Fallback failsafe
                setTimeout(() => this.stopLoader(), 8000);
                
                // Start tracking
                this.startLoader();
            },
            
            startLoader() {
                // Wait 400ms before actually showing the loader. 
                // If it loads faster than that, the user never sees it!
                if (this.pendingTimeout) clearTimeout(this.pendingTimeout);
                this.pendingTimeout = setTimeout(() => {
                    this.isLoading = true;
                    this.startedAt = performance.now();
                    if(this.animationFrame) cancelAnimationFrame(this.animationFrame);
                    this.animationFrame = requestAnimationFrame((n) => this.render(n));
                }, 400); // 400ms tolerance
            },
            
            stopLoader() {
                // If it finished before 300ms, cancel the timeout so it never shows!
                if (this.pendingTimeout) {
                    clearTimeout(this.pendingTimeout);
                }
                this.isLoading = false;
            }
        }))
    })
</script>
@endpersist
