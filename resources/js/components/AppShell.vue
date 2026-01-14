<template>
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0">
            <div class="h-full w-full bg-gradient-to-br from-icloud-blue/40 via-icloud-slate to-icloud-purple/40"></div>
            <div class="absolute -top-32 left-10 h-96 w-96 rounded-full bg-icloud-pink/40 blur-[120px]"></div>
            <div class="absolute bottom-0 right-0 h-[28rem] w-[28rem] rounded-full bg-icloud-teal/30 blur-[140px]"></div>
        </div>

        <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col gap-8 px-6 py-10">
            <header class="flex flex-wrap items-center justify-between gap-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-white/50">CloudDrive</p>
                    <h1 class="text-3xl font-semibold text-white">你的 Apple 风格网盘</h1>
                    <p class="mt-2 text-sm text-white/60">当前存储：{{ providerLabel }} · 路径：{{ currentPath }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button class="icloud-button" @click="toggleCreateFolder">创建文件夹</button>
                    <label class="icloud-button cursor-pointer">
                        上传
                        <input ref="fileInput" type="file" class="hidden" @change="handleUpload" />
                    </label>
                </div>
            </header>

            <section class="glass-panel p-6">
                <div class="flex flex-col gap-6 lg:flex-row">
                    <aside class="flex w-full flex-col gap-4 lg:w-64">
                        <div class="text-xs uppercase tracking-[0.2em] text-white/40">工作区</div>
                        <nav class="space-y-2">
                            <button class="icloud-nav-item" @click="jumpTo('/')">
                                <span class="h-2 w-2 rounded-full bg-icloud-teal"></span>
                                最近使用
                            </button>
                            <button class="icloud-nav-item" @click="jumpTo('/共享空间')">
                                <span class="h-2 w-2 rounded-full bg-icloud-blue"></span>
                                共享空间
                            </button>
                            <button class="icloud-nav-item" @click="jumpTo('/收藏夹')">
                                <span class="h-2 w-2 rounded-full bg-icloud-pink"></span>
                                收藏夹
                            </button>
                            <button class="icloud-nav-item" @click="jumpTo('/回收站')">
                                <span class="h-2 w-2 rounded-full bg-white/70"></span>
                                回收站
                            </button>
                        </nav>
                        <div class="glass-panel p-4">
                            <p class="text-xs text-white/60">连接存储</p>
                            <div class="mt-3 flex flex-col gap-2 text-sm text-white">
                                <div class="flex items-center justify-between">
                                    <span>WebDAV</span>
                                    <span class="text-white/60">演示模式</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>S3</span>
                                    <span class="text-white/60">演示模式</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>本地</span>
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
                                <button class="icloud-button" @click="refresh">刷新</button>
                                <button class="icloud-button" @click="jumpTo('/')">回到根目录</button>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="glass-panel p-5">
                                <p class="text-sm text-white/60">空间占用</p>
                                <p class="mt-2 text-2xl font-semibold">{{ formatSize(stats.size) }}</p>
                                <p class="text-xs text-white/50">共 {{ stats.files }} 个文件 · {{ stats.folders }} 个文件夹</p>
                            </div>
                            <div class="glass-panel p-5">
                                <p class="text-sm text-white/60">传输状态</p>
                                <p class="mt-2 text-2xl font-semibold">
                                    {{ isUploading ? '上传中' : '待命' }}
                                </p>
                                <p class="text-xs text-white/50">
                                    {{ loading ? '正在同步目录...' : '支持拖拽/选择上传文件' }}
                                </p>
                            </div>
                        </div>

                        <div class="glass-panel p-6">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm text-white/60">文件浏览器</p>
                                    <h3 class="text-xl font-semibold">{{ breadcrumbLabel }}</h3>
                                </div>
                                <div class="flex items-center gap-2 text-xs text-white/60">
                                    <span v-for="(crumb, index) in breadcrumbs" :key="crumb.path" class="flex items-center gap-2">
                                        <button class="hover:text-white" @click="jumpTo(crumb.path)">{{ crumb.label }}</button>
                                        <span v-if="index < breadcrumbs.length - 1">/</span>
                                    </span>
                                </div>
                            </div>

                            <div v-if="showCreate" class="mt-5 flex flex-wrap items-center gap-3">
                                <input
                                    v-model="folderName"
                                    type="text"
                                    placeholder="输入文件夹名称"
                                    class="icloud-input"
                                />
                                <button class="icloud-button" :disabled="creatingFolder" @click="createFolder">
                                    {{ creatingFolder ? '创建中...' : '确认创建' }}
                                </button>
                                <button class="icloud-button" @click="toggleCreateFolder">取消</button>
                            </div>

                            <div v-if="error" class="mt-4 rounded-2xl bg-icloud-pink/20 px-4 py-3 text-sm text-white">
                                {{ error }}
                            </div>

                            <div class="mt-6 grid gap-3">
                                <button
                                    v-if="currentPath !== '/'"
                                    class="icloud-file-row"
                                    @click="jumpTo(parentPath)"
                                >
                                    <div class="flex items-center gap-3">
                                        <span class="icloud-badge">..</span>
                                        <div>
                                            <p class="text-sm font-medium">返回上一级</p>
                                            <p class="text-xs text-white/50">{{ parentPath }}</p>
                                        </div>
                                    </div>
                                </button>

                                <button
                                    v-for="item in items"
                                    :key="item.path"
                                    class="icloud-file-row"
                                    @click="item.type === 'folder' ? jumpTo(item.path) : null"
                                >
                                    <div class="flex items-center gap-3">
                                        <span class="icloud-badge">{{ item.type === 'folder' ? '文件夹' : '文件' }}</span>
                                        <div>
                                            <p class="text-sm font-medium">{{ item.name }}</p>
                                            <p class="text-xs text-white/50">
                                                {{ item.type === 'folder' ? '目录' : formatSize(item.size) }}
                                                <span v-if="item.updated_at">· {{ formatTime(item.updated_at) }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <span class="text-xs text-white/60">{{ item.type === 'folder' ? '进入' : '已同步' }}</span>
                                </button>

                                <div v-if="items.length === 0" class="rounded-2xl bg-white/5 px-4 py-6 text-center text-sm text-white/60">
                                    当前目录为空，创建文件夹或上传文件开始吧。
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

<script setup>
import { computed, onMounted, ref } from 'vue';

const items = ref([]);
const currentPath = ref('/');
const provider = ref('local');
const stats = ref({ folders: 0, files: 0, size: 0 });
const loading = ref(false);
const error = ref('');
const showCreate = ref(false);
const folderName = ref('');
const creatingFolder = ref(false);
const isUploading = ref(false);
const fileInput = ref(null);

const providerLabel = computed(() => {
    if (provider.value === 's3') {
        return 'Amazon S3';
    }
    if (provider.value === 'webdav') {
        return 'WebDAV';
    }
    return '本地存储';
});

const breadcrumbs = computed(() => {
    const normalized = currentPath.value === '/' ? '' : currentPath.value.replace(/^\//, '');
    const segments = normalized === '' ? [] : normalized.split('/');
    const crumbList = [{ label: '根目录', path: '/' }];

    let accumulated = '';
    segments.forEach((segment) => {
        accumulated += `/${segment}`;
        crumbList.push({ label: segment, path: accumulated });
    });

    return crumbList;
});

const breadcrumbLabel = computed(() => {
    return breadcrumbs.value[breadcrumbs.value.length - 1]?.label ?? '根目录';
});

const parentPath = computed(() => {
    if (currentPath.value === '/') {
        return '/';
    }
    const segments = currentPath.value.split('/').filter(Boolean);
    segments.pop();
    return segments.length === 0 ? '/' : `/${segments.join('/')}`;
});

const fetchItems = async (path = currentPath.value) => {
    loading.value = true;
    error.value = '';

    try {
        const response = await fetch(`/api/storage/items?path=${encodeURIComponent(path)}`);
        if (!response.ok) {
            throw new Error('目录获取失败');
        }
        const data = await response.json();
        items.value = data.items ?? [];
        currentPath.value = data.path ?? '/';
        provider.value = data.provider ?? 'local';
        stats.value = data.stats ?? { folders: 0, files: 0, size: 0 };
    } catch (fetchError) {
        error.value = fetchError.message ?? '网络异常，请稍后重试。';
    } finally {
        loading.value = false;
    }
};

const refresh = () => fetchItems();

const jumpTo = (path) => {
    fetchItems(path);
};

const toggleCreateFolder = () => {
    showCreate.value = !showCreate.value;
    if (!showCreate.value) {
        folderName.value = '';
    }
};

const createFolder = async () => {
    if (!folderName.value.trim()) {
        error.value = '请输入文件夹名称。';
        return;
    }

    creatingFolder.value = true;
    error.value = '';

    try {
        const response = await fetch('/api/storage/folders', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                path: currentPath.value,
                name: folderName.value.trim(),
            }),
        });

        if (!response.ok) {
            throw new Error('文件夹创建失败');
        }

        folderName.value = '';
        showCreate.value = false;
        await fetchItems();
    } catch (createError) {
        error.value = createError.message ?? '创建失败，请稍后再试。';
    } finally {
        creatingFolder.value = false;
    }
};

const handleUpload = async (event) => {
    const file = event.target.files?.[0];
    if (!file) {
        return;
    }

    const formData = new FormData();
    formData.append('path', currentPath.value);
    formData.append('file', file);

    isUploading.value = true;
    error.value = '';

    try {
        const response = await fetch('/api/storage/upload', {
            method: 'POST',
            body: formData,
        });

        if (!response.ok) {
            throw new Error('上传失败');
        }

        if (fileInput.value) {
            fileInput.value.value = '';
        }
        await fetchItems();
    } catch (uploadError) {
        error.value = uploadError.message ?? '上传失败，请稍后再试。';
    } finally {
        isUploading.value = false;
    }
};

const formatSize = (size) => {
    if (!size) {
        return '0 B';
    }
    const units = ['B', 'KB', 'MB', 'GB', 'TB'];
    let index = 0;
    let value = size;
    while (value >= 1024 && index < units.length - 1) {
        value /= 1024;
        index += 1;
    }
    return `${value.toFixed(1)} ${units[index]}`;
};

const formatTime = (iso) => {
    if (!iso) {
        return '';
    }
    const date = new Date(iso);
    if (Number.isNaN(date.getTime())) {
        return '';
    }
    return date.toLocaleString('zh-CN', {
        hour12: false,
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
    });
};

onMounted(() => {
    fetchItems('/');
});
</script>
