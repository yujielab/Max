<template>
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0">
            <div class="h-full w-full bg-gradient-to-br from-icloud-blue/40 via-icloud-slate to-icloud-purple/40"></div>
            <div class="absolute -top-32 left-10 h-96 w-96 rounded-full bg-icloud-pink/40 blur-[120px]"></div>
            <div class="absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-icloud-teal/30 blur-[140px]"></div>
        </div>

        <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col gap-8 px-6 py-10">
            <header class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-white/50">CloudDrive</p>
                    <h1 class="text-3xl font-semibold text-white">你的 Apple 风格网盘</h1>
                </div>
                <div class="flex items-center gap-3">
                    <button class="icloud-button">创建文件夹</button>
                    <button class="icloud-button">上传</button>
                </div>
            </header>

            <section class="glass-panel p-6">
                <div class="flex flex-col gap-6 lg:flex-row">
                    <aside class="flex w-full flex-col gap-4 lg:w-64">
                        <div class="text-xs uppercase tracking-[0.2em] text-white/40">工作区</div>
                        <nav class="space-y-2">
                            <div class="icloud-nav-item">
                                <span class="h-2 w-2 rounded-full bg-icloud-teal"></span>
                                最近使用
                            </div>
                            <div class="icloud-nav-item">
                                <span class="h-2 w-2 rounded-full bg-icloud-blue"></span>
                                共享空间
                            </div>
                            <div class="icloud-nav-item">
                                <span class="h-2 w-2 rounded-full bg-icloud-pink"></span>
                                收藏夹
                            </div>
                            <div class="icloud-nav-item">
                                <span class="h-2 w-2 rounded-full bg-white/70"></span>
                                回收站
                            </div>
                        </nav>
                        <div class="glass-panel p-4">
                            <p class="text-xs text-white/60">连接存储</p>
                            <div class="mt-3 flex flex-col gap-2 text-sm text-white">
                                <div class="flex items-center justify-between">
                                    <span>WebDAV</span>
                                    <span class="text-white/60">在线</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>S3</span>
                                    <span class="text-white/60">在线</span>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <div class="flex-1 space-y-6">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-white/40">同步中心</p>
                                <h2 class="text-2xl font-semibold text-white">所有文件，一处掌控</h2>
                            </div>
                            <div class="flex items-center gap-3">
                                <button class="icloud-button">智能标签</button>
                                <button class="icloud-button">排序</button>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="glass-panel p-5">
                                <p class="text-sm text-white/60">性能状态</p>
                                <p class="mt-2 text-2xl font-semibold">2.4 GB/s</p>
                                <p class="text-xs text-white/50">多线路并发与增量同步</p>
                            </div>
                            <div class="glass-panel p-5">
                                <p class="text-sm text-white/60">智能缓存</p>
                                <p class="mt-2 text-2xl font-semibold">88% 命中率</p>
                                <p class="text-xs text-white/50">边缘节点与本地预取</p>
                            </div>
                        </div>

                        <div class="glass-panel p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-white/60">最新文件</p>
                                    <h3 class="text-xl font-semibold">设计稿 / 交付</h3>
                                </div>
                                <span class="rounded-full bg-white/10 px-3 py-1 text-xs text-white/70">iCloud 风格动效</span>
                            </div>
                            <div class="mt-6 grid gap-3">
                                <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3">
                                    <div>
                                        <p class="text-sm font-medium">Vision Pro 发布会.key</p>
                                        <p class="text-xs text-white/50">3.2 GB · 自动同步</p>
                                    </div>
                                    <span class="text-xs text-white/60">刚刚</span>
                                </div>
                                <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3">
                                    <div>
                                        <p class="text-sm font-medium">UI 组件库.fig</p>
                                        <p class="text-xs text-white/50">1.1 GB · WebDAV</p>
                                    </div>
                                    <span class="text-xs text-white/60">5 分钟前</span>
                                </div>
                                <div class="flex items-center justify-between rounded-2xl bg-white/5 px-4 py-3">
                                    <div>
                                        <p class="text-sm font-medium">营销视频.mov</p>
                                        <p class="text-xs text-white/50">8.6 GB · S3</p>
                                    </div>
                                    <span class="text-xs text-white/60">1 小时前</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="flex flex-wrap items-center justify-between gap-4 text-xs text-white/50">
                <span>端到端加密 · 智能同步 · 多云加速</span>
                <span>Laravel 12 · Vue 3 · TailwindCSS</span>
            </footer>
        </div>
    </div>
</template>
