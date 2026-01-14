<template>
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0">
            <div class="h-full w-full bg-gradient-to-br from-icloud-blue/40 via-icloud-slate to-icloud-purple/40"></div>
            <div class="absolute -top-32 left-10 h-96 w-96 animate-slow-float rounded-full bg-icloud-pink/40 blur-[120px]"></div>
            <div class="absolute bottom-0 right-0 h-[28rem] w-[28rem] animate-slow-float rounded-full bg-icloud-teal/30 blur-[140px]"></div>
        </div>

        <div class="relative mx-auto flex min-h-screen max-w-6xl flex-col gap-8 px-6 py-10">
            <header class="flex flex-wrap items-start justify-between gap-6">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-white/50">CloudDrive</p>
                    <h1 class="text-3xl font-semibold text-white">你的 Apple 风格网盘</h1>
                    <p class="mt-2 text-sm text-white/60">
                        当前存储：{{ providerLabel }} · 路径：{{ currentPath }}
                    </p>
                    <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-white/60">
                        <span class="icloud-pill">
                            {{ activation.completed ? '已激活' : '未激活' }}
                        </span>
                        <span class="icloud-pill">{{ syncLabel }}</span>
                        <span class="icloud-pill">{{ isBusy ? '忙碌中' : '就绪' }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <button class="icloud-button-secondary" @click="openActivation">
                        {{ activation.completed ? '配置中心' : '开始配置' }}
                    </button>
                    <button class="icloud-button" @click="toggleCreateFolder" :disabled="activationOpen">
                        创建文件夹
                    </button>
                    <label class="icloud-button cursor-pointer" :class="{ 'opacity-50': activationOpen }">
                        上传
                        <input ref="fileInput" type="file" class="hidden" @change="handleUpload" :disabled="activationOpen" />
                    </label>
                </div>
            </header>

            <transition name="fade-slide" mode="out-in">
                <section v-if="activationOpen" key="activation" class="glass-panel p-8">
                    <div class="flex flex-col gap-8 lg:flex-row">
                        <div class="flex-1 space-y-6">
                            <div>
                                <p class="text-xs uppercase tracking-[0.3em] text-white/40">首次激活</p>
                                <h2 class="mt-3 text-3xl font-semibold">一键安装与配置</h2>
                                <p class="mt-3 text-sm text-white/70">
                                    选择存储类型，填入必要信息，系统将自动完成初始化。无需手动修改复杂配置。
                                </p>
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="glass-panel p-5">
                                    <p class="text-sm text-white/60">推荐流程</p>
                                    <ul class="mt-3 space-y-2 text-sm text-white/80">
                                        <li class="flex items-center gap-2">
                                            <span class="icloud-dot"></span>
                                            选择存储驱动并快速完成配置
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <span class="icloud-dot"></span>
                                            一键初始化目录，自动生成安全策略
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <span class="icloud-dot"></span>
                                            激活后即可上传、同步与共享
                                        </li>
                                    </ul>
                                </div>
                                <div class="glass-panel p-5">
                                    <p class="text-sm text-white/60">体验预览</p>
                                    <div class="mt-4 space-y-3 text-sm text-white/80">
                                        <div class="flex items-center justify-between">
                                            <span>自动检测</span>
                                            <span class="icloud-badge">已启用</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>多云切换</span>
                                            <span class="icloud-badge">可选</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>错误自愈</span>
                                            <span class="icloud-badge">智能</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="glass-panel p-5">
                                <p class="text-sm text-white/60">快速激活</p>
                                <div class="mt-4 flex flex-wrap gap-3">
                                    <button class="icloud-button-primary" @click="applyQuickSetup('local')">一键本地安装</button>
                                    <button class="icloud-button-secondary" @click="applyQuickSetup('webdav')">快速 WebDAV</button>
                                    <button class="icloud-button-secondary" @click="applyQuickSetup('s3')">快速 S3</button>
                                </div>
                                <p class="mt-3 text-xs text-white/50">
                                    一键配置仅保存到浏览器本地，不会上传任何凭据。
                                </p>
                            </div>
                        </div>

                        <div class="flex w-full flex-col gap-4 lg:w-96">
                            <div class="glass-panel p-5">
                                <p class="text-xs uppercase tracking-[0.3em] text-white/40">配置向导</p>
                                <div class="mt-4 space-y-4">
                                    <div class="space-y-2">
                                        <p class="text-sm text-white/70">选择存储类型</p>
                                        <div class="grid gap-2">
                                            <button
                                                v-for="option in providerOptions"
                                                :key="option.value"
                                                class="icloud-option"
                                                :class="{ 'is-active': activationDraft.provider === option.value }"
                                                @click="activationDraft.provider = option.value"
                                            >
                                                <div>
                                                    <p class="text-sm font-medium">{{ option.label }}</p>
                                                    <p class="text-xs text-white/50">{{ option.description }}</p>
                                                </div>
                                                <span class="icloud-pill">{{ option.tag }}</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <p class="text-sm text-white/70">配置参数</p>
                                        <div v-if="activationDraft.provider === 'local'" class="space-y-3">
                                            <input
                                                v-model="activationDraft.localRoot"
                                                type="text"
                                                placeholder="存储目录 (默认 cloud-drive)"
                                                class="icloud-input"
                                            />
                                            <p class="text-xs text-white/50">默认将文件保存到本地存储目录。</p>
                                        </div>
                                        <div v-else-if="activationDraft.provider === 'webdav'" class="space-y-3">
                                            <input
                                                v-model="activationDraft.webdavBaseUri"
                                                type="text"
                                                placeholder="WebDAV 地址"
                                                class="icloud-input"
                                            />
                                            <div class="grid gap-3 sm:grid-cols-2">
                                                <input v-model="activationDraft.webdavUsername" type="text" placeholder="用户名" class="icloud-input" />
                                                <input v-model="activationDraft.webdavPassword" type="password" placeholder="密码" class="icloud-input" />
                                            </div>
                                        </div>
                                        <div v-else class="space-y-3">
                                            <input v-model="activationDraft.s3Region" type="text" placeholder="区域 (例如 ap-southeast-1)" class="icloud-input" />
                                            <input v-model="activationDraft.s3Bucket" type="text" placeholder="Bucket 名称" class="icloud-input" />
                                        </div>
                                    </div>

                                    <div class="rounded-2xl bg-white/5 p-4 text-xs text-white/60">
                                        <p>配置预览</p>
                                        <p class="mt-2">驱动：{{ activationPreviewLabel }}</p>
                                        <p class="mt-1">路径：{{ activationPreviewPath }}</p>
                                    </div>

                                    <div v-if="activationError" class="rounded-2xl bg-icloud-pink/20 px-4 py-3 text-sm text-white">
                                        {{ activationError }}
                                    </div>

                                    <div class="flex flex-wrap gap-3">
                                        <button class="icloud-button-primary" @click="completeActivation">完成激活</button>
                                        <button class="icloud-button-secondary" @click="cancelActivation">
                                            暂不配置
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="glass-panel p-5">
                                <p class="text-sm text-white/60">激活后你将获得</p>
                                <ul class="mt-3 space-y-2 text-sm text-white/80">
                                    <li class="flex items-center gap-2"><span class="icloud-dot"></span>一键上传与拖拽同步</li>
                                    <li class="flex items-center gap-2"><span class="icloud-dot"></span>可视化目录管理</li>
                                    <li class="flex items-center gap-2"><span class="icloud-dot"></span>错误实时提示与恢复</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>
            </transition>

            <transition name="fade-slide" mode="out-in">
                <section v-else key="workspace" class="glass-panel p-6">
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
                                        <span class="text-white/60">{{ activation.webdavLabel }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span>S3</span>
                                        <span class="text-white/60">{{ activation.s3Label }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span>本地</span>
                                        <span class="text-white/60">{{ activation.localLabel }}</span>
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
                                    <button class="icloud-button" @click="refresh" :disabled="loading">刷新</button>
                                    <button class="icloud-button" @click="jumpTo('/')" :disabled="loading">回到根目录</button>
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

                            <div v-if="errorState" class="error-panel">
                                <div>
                                    <p class="text-sm font-semibold">{{ errorState.title }}</p>
                                    <p class="mt-1 text-xs text-white/60">{{ errorState.detail }}</p>
                                </div>
                                <button class="icloud-button-secondary" @click="retryLast">重试</button>
                            </div>

                            <div v-if="notices.length" class="space-y-3">
                                <div
                                    v-for="notice in notices"
                                    :key="notice.id"
                                    class="glass-panel flex items-center justify-between gap-3 p-4 text-sm"
                                >
                                    <div>
                                        <p class="font-semibold">{{ notice.title }}</p>
                                        <p class="text-xs text-white/60">{{ notice.message }}</p>
                                    </div>
                                    <button class="icloud-button-secondary" @click="dismissNotice(notice.id)">关闭</button>
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
                                    <button class="icloud-button-secondary" @click="toggleCreateFolder">取消</button>
                                </div>

                                <div class="mt-6 grid gap-3">
                                    <button
                                        v-if="currentPath !== '/'"
                                        class="icloud-file-row group"
                                        @click="jumpTo(parentPath)"
                                    >
                                        <div class="flex items-center gap-3">
                                            <span class="icloud-badge">..</span>
                                            <div>
                                                <p class="text-sm font-medium">返回上一级</p>
                                                <p class="text-xs text-white/50">{{ parentPath }}</p>
                                            </div>
                                        </div>
                                        <span class="text-xs text-white/60 group-hover:text-white">返回</span>
                                    </button>

                                    <button
                                        v-for="item in items"
                                        :key="item.path"
                                        class="icloud-file-row group"
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
                                        <span class="text-xs text-white/60 group-hover:text-white">
                                            {{ item.type === 'folder' ? '进入' : '已同步' }}
                                        </span>
                                    </button>

                                    <div
                                        v-if="items.length === 0 && !loading"
                                        class="rounded-2xl bg-white/5 px-4 py-6 text-center text-sm text-white/60"
                                    >
                                        当前目录为空，创建文件夹或上传文件开始吧。
                                    </div>
                                    <div v-if="loading" class="rounded-2xl bg-white/5 px-4 py-6 text-center text-sm text-white/60">
                                        正在加载目录，请稍候...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </transition>

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
const errorState = ref(null);
const showCreate = ref(false);
const folderName = ref('');
const creatingFolder = ref(false);
const isUploading = ref(false);
const fileInput = ref(null);
const notices = ref([]);
const activationOpen = ref(false);
const activationError = ref('');
const activationDraft = ref({
    provider: 'local',
    localRoot: 'cloud-drive',
    webdavBaseUri: '',
    webdavUsername: '',
    webdavPassword: '',
    s3Region: '',
    s3Bucket: '',
});
const activation = ref({
    completed: false,
    provider: 'local',
    config: {},
    localLabel: '在线',
    webdavLabel: '未连接',
    s3Label: '未连接',
});
const lastAction = ref('');

const activationStorageKey = 'cloudrive_activation_v1';

const providerOptions = [
    {
        value: 'local',
        label: '本地存储',
        description: '最快速的本地目录模式',
        tag: '推荐',
    },
    {
        value: 'webdav',
        label: 'WebDAV',
        description: '接入外部 WebDAV 盘',
        tag: '同步',
    },
    {
        value: 's3',
        label: 'S3 兼容存储',
        description: '连接对象存储服务',
        tag: '稳定',
    },
];

const providerLabel = computed(() => getProviderLabel(provider.value));
const syncLabel = computed(() => (loading.value ? '同步中' : '已同步'));
const isBusy = computed(() => loading.value || creatingFolder.value || isUploading.value);

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

const activationPreviewLabel = computed(() => getProviderLabel(activationDraft.value.provider));
const activationPreviewPath = computed(() => {
    if (activationDraft.value.provider === 'local') {
        return activationDraft.value.localRoot || 'cloud-drive';
    }
    if (activationDraft.value.provider === 'webdav') {
        return activationDraft.value.webdavBaseUri || '未填写地址';
    }
    return activationDraft.value.s3Bucket || '未填写 Bucket';
});

const fetchItems = async (path = currentPath.value) => {
    if (activationOpen.value) {
        return;
    }
    loading.value = true;
    clearError();
    lastAction.value = 'fetch';

    try {
        const data = await requestJson(`/api/storage/items?path=${encodeURIComponent(path)}`, '目录获取失败');
        items.value = data.items ?? [];
        currentPath.value = data.path ?? '/';
        provider.value = data.provider ?? 'local';
        stats.value = data.stats ?? { folders: 0, files: 0, size: 0 };
    } catch (fetchError) {
        setError('目录同步失败', fetchError.message ?? '网络异常，请稍后重试。');
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
        setError('创建失败', '请输入文件夹名称。');
        return;
    }

    creatingFolder.value = true;
    clearError();
    lastAction.value = 'create';

    try {
        await requestJson(
            '/api/storage/folders',
            '文件夹创建失败',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    path: currentPath.value,
                    name: folderName.value.trim(),
                }),
            }
        );

        folderName.value = '';
        showCreate.value = false;
        pushNotice('success', '创建完成', '新文件夹已准备好。');
        await fetchItems();
    } catch (createError) {
        setError('创建失败', createError.message ?? '创建失败，请稍后再试。');
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
    clearError();
    lastAction.value = 'upload';

    try {
        await requestJson('/api/storage/upload', '上传失败', {
            method: 'POST',
            body: formData,
        });

        if (fileInput.value) {
            fileInput.value.value = '';
        }
        pushNotice('success', '上传完成', `${file.name} 已同步。`);
        await fetchItems();
    } catch (uploadError) {
        setError('上传失败', uploadError.message ?? '上传失败，请稍后再试。');
    } finally {
        isUploading.value = false;
    }
};

const retryLast = () => {
    if (lastAction.value === 'fetch') {
        fetchItems();
        return;
    }
    if (lastAction.value === 'create') {
        createFolder();
        return;
    }
    if (lastAction.value === 'upload') {
        if (fileInput.value?.files?.length) {
            handleUpload({ target: fileInput.value });
        }
    }
};

const openActivation = () => {
    activationError.value = '';
    if (activation.value.completed) {
        activationDraft.value = {
            provider: activation.value.provider ?? 'local',
            localRoot: activation.value.config?.localRoot ?? 'cloud-drive',
            webdavBaseUri: activation.value.config?.webdavBaseUri ?? '',
            webdavUsername: activation.value.config?.webdavUsername ?? '',
            webdavPassword: '',
            s3Region: activation.value.config?.s3Region ?? '',
            s3Bucket: activation.value.config?.s3Bucket ?? '',
        };
    }
    activationOpen.value = true;
};

const cancelActivation = () => {
    activationError.value = '';
    activationOpen.value = false;
    if (items.value.length === 0) {
        fetchItems('/');
    }
};

const applyQuickSetup = (providerType) => {
    activationDraft.value = {
        provider: providerType,
        localRoot: 'cloud-drive',
        webdavBaseUri: providerType === 'webdav' ? 'https://dav.example.com/remote.php/dav/files' : '',
        webdavUsername: '',
        webdavPassword: '',
        s3Region: providerType === 's3' ? 'ap-southeast-1' : '',
        s3Bucket: providerType === 's3' ? 'icloud-demo' : '',
    };
};

const completeActivation = () => {
    activationError.value = '';
    if (!activationDraft.value.provider) {
        activationError.value = '请选择存储类型。';
        return;
    }
    if (activationDraft.value.provider === 'webdav' && !activationDraft.value.webdavBaseUri) {
        activationError.value = '请填写 WebDAV 地址。';
        return;
    }
    if (activationDraft.value.provider === 's3' && !activationDraft.value.s3Bucket) {
        activationError.value = '请填写 S3 Bucket。';
        return;
    }

    activation.value = {
        completed: true,
        provider: activationDraft.value.provider,
        config: {
            localRoot: activationDraft.value.localRoot,
            webdavBaseUri: activationDraft.value.webdavBaseUri,
            webdavUsername: activationDraft.value.webdavUsername,
            s3Region: activationDraft.value.s3Region,
            s3Bucket: activationDraft.value.s3Bucket,
        },
        localLabel: activationDraft.value.provider === 'local' ? '已启用' : '待命',
        webdavLabel: activationDraft.value.provider === 'webdav' ? '已启用' : '待命',
        s3Label: activationDraft.value.provider === 's3' ? '已启用' : '待命',
    };

    persistActivation();
    activationOpen.value = false;
    pushNotice('success', '激活成功', '配置已保存到本地，随时可重新修改。');
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

const requestJson = async (url, fallbackMessage, options = {}) => {
    const response = await fetch(url, options);
    let data = null;
    try {
        data = await response.json();
    } catch {
        data = null;
    }

    if (!response.ok) {
        const message = data?.message ?? fallbackMessage;
        throw new Error(message);
    }
    return data ?? {};
};

const getProviderLabel = (value) => {
    if (value === 's3') {
        return 'Amazon S3';
    }
    if (value === 'webdav') {
        return 'WebDAV';
    }
    return '本地存储';
};

const setError = (title, detail) => {
    errorState.value = {
        title,
        detail,
    };
};

const clearError = () => {
    errorState.value = null;
};

const pushNotice = (type, title, message) => {
    const id = `${Date.now()}-${Math.random()}`;
    notices.value.push({ id, type, title, message });
    setTimeout(() => dismissNotice(id), 5000);
};

const dismissNotice = (id) => {
    notices.value = notices.value.filter((notice) => notice.id !== id);
};

const persistActivation = () => {
    localStorage.setItem(activationStorageKey, JSON.stringify(activation.value));
};

const loadActivation = () => {
    try {
        const stored = JSON.parse(localStorage.getItem(activationStorageKey));
        if (stored?.completed) {
            activation.value = {
                ...activation.value,
                ...stored,
            };
            activationOpen.value = false;
        } else {
            activationOpen.value = true;
        }
    } catch {
        activationOpen.value = true;
    }
};

onMounted(() => {
    loadActivation();
    fetchItems('/');
});
</script>
