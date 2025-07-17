// resources/js/asset-overview-video.js

class AssetOverviewVideoManager {
    constructor() {
        this.videos = new Map();
        this.userInteracted = false;
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.initAssetOverviewVideos();
            this.setupGlobalEventListeners();
        });
    }

    initAssetOverviewVideos() {
        const videoContainers = document.querySelectorAll('.asset-overview-list .video-container');
        console.log('Found video containers:', videoContainers.length);

        videoContainers.forEach((container, index) => {
            const video = container.querySelector('video');
            if (!video) return;

            const videoManager = {
                video,
                container,
                index,
                loadingIndicator: null,
                customControls: null,
                isPlaying: false,
                progressInterval: null
            };

            this.videos.set(video, videoManager);
            this.setupVideoManager(videoManager);
        });
    }

    setupVideoManager(videoManager) {
        this.setupVideoHandlers(videoManager);
        this.setupVideoLazyLoading(videoManager);

        if (!videoManager.video.hasAttribute('controls')) {
            this.setupCustomControls(videoManager);
            // Инициализируем состояние кнопки mute после создания контролов
            this.initializeMuteButtonState(videoManager);
        }

        this.checkAutoplayCapability(videoManager);
    }

    setupCustomControls(videoManager) {
        const { video, container, index } = videoManager;

        // Создаем контейнер для кастомных контролов
        const controlsContainer = document.createElement('div');
        controlsContainer.className = 'video-custom-controls absolute inset-0 flex flex-col justify-between p-4 opacity-0 hover:opacity-100 transition-all duration-300 pointer-events-none';

        // Верхняя панель с индикатором загрузки и кнопкой звука
        const topPanel = document.createElement('div');
        topPanel.className = 'flex justify-between items-start';

        // Кнопка звука
        const muteButton = this.createMuteButton(videoManager);
        topPanel.appendChild(muteButton);

        // Центральная кнопка play/pause
        const playButton = this.createPlayButton(videoManager);

        // Нижняя панель с прогресс-баром и временем
        const bottomPanel = document.createElement('div');
        bottomPanel.className = 'flex flex-col space-y-2';

        // Прогресс-бар
        const progressContainer = this.createProgressBar(videoManager);
        bottomPanel.appendChild(progressContainer);

        // Панель с временем и полноэкранным режимом
        const timePanel = document.createElement('div');
        timePanel.className = 'flex justify-between items-center text-white text-sm';

        const timeDisplay = document.createElement('span');
        timeDisplay.className = 'time-display font-mono';
        timeDisplay.textContent = '0:00 / 0:00';

        const fullscreenButton = this.createFullscreenButton(videoManager);

        timePanel.appendChild(timeDisplay);
        timePanel.appendChild(fullscreenButton);
        bottomPanel.appendChild(timePanel);

        // Собираем все элементы
        controlsContainer.appendChild(topPanel);
        controlsContainer.appendChild(playButton);
        controlsContainer.appendChild(bottomPanel);

        container.appendChild(controlsContainer);
        videoManager.customControls = controlsContainer;

        // Обработчики для показа/скрытия контролов
        this.setupControlsVisibility(videoManager);

        // Обновляем время
        this.updateTimeDisplay(videoManager);
    }

    createPlayButton(videoManager) {
        const playButton = document.createElement('button');
        playButton.className = 'play-pause-btn flex items-center justify-center w-20 h-20 bg-black bg-opacity-60 hover:bg-opacity-80 text-white rounded-full transition-all duration-300 transform hover:scale-110 pointer-events-auto backdrop-blur-sm';
        playButton.setAttribute('aria-label', 'Play/Pause video');

        playButton.innerHTML = `
            <svg class="play-icon w-8 h-8 ml-1" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 5v14l11-7z"/>
            </svg>
            <svg class="pause-icon w-8 h-8 hidden" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
            </svg>
        `;

        playButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.togglePlayPause(videoManager);
        });

        return playButton;
    }

    createMuteButton(videoManager) {
        const muteButton = document.createElement('button');
        muteButton.className = 'mute-btn w-10 h-10 bg-black bg-opacity-60 hover:bg-opacity-80 text-white rounded-full flex items-center justify-center transition-all duration-300 pointer-events-auto backdrop-blur-sm';
        muteButton.setAttribute('aria-label', 'Mute/Unmute video');

        muteButton.innerHTML = `
            <svg class="volume-on w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
            </svg>
            <svg class="volume-off w-5 h-5 hidden" fill="currentColor" viewBox="0 0 24 24">
                <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
            </svg>
        `;

        muteButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggleMute(videoManager);
        });

        return muteButton;
    }

    createProgressBar(videoManager) {
        const progressContainer = document.createElement('div');
        progressContainer.className = 'progress-container w-full h-2 bg-black bg-opacity-40 rounded-full cursor-pointer pointer-events-auto hover:h-3 transition-all duration-200';

        const progressBar = document.createElement('div');
        progressBar.className = 'progress-bar h-full bg-blue-500 rounded-full transition-all duration-100 relative';
        progressBar.style.width = '0%';

        const progressThumb = document.createElement('div');
        progressThumb.className = 'progress-thumb absolute -right-2 -top-1 w-4 h-4 bg-blue-500 rounded-full opacity-0 hover:opacity-100 transition-opacity duration-200 shadow-lg';
        progressBar.appendChild(progressThumb);

        progressContainer.appendChild(progressBar);

        // Обработчики для прогресс-бара
        this.setupProgressBarHandlers(videoManager, progressContainer, progressBar);

        return progressContainer;
    }

    createFullscreenButton(videoManager) {
        const fullscreenButton = document.createElement('button');
        fullscreenButton.className = 'fullscreen-btn w-8 h-8 text-white hover:text-blue-400 transition-colors duration-200 pointer-events-auto';
        fullscreenButton.setAttribute('aria-label', 'Toggle fullscreen');

        fullscreenButton.innerHTML = `
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
            </svg>
        `;

        fullscreenButton.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            this.toggleFullscreen(videoManager);
        });

        return fullscreenButton;
    }

    setupProgressBarHandlers(videoManager, progressContainer, progressBar) {
        const { video } = videoManager;
        let isDragging = false;

        const updateProgress = () => {
            if (!isDragging && video.duration) {
                const progress = (video.currentTime / video.duration) * 100;
                progressBar.style.width = `${progress}%`;
            }
        };

        const handleProgressClick = (e) => {
            const rect = progressContainer.getBoundingClientRect();
            const percent = (e.clientX - rect.left) / rect.width;
            const newTime = percent * video.duration;

            if (newTime >= 0 && newTime <= video.duration) {
                video.currentTime = newTime;
                updateProgress();
            }
        };

        const handleMouseMove = (e) => {
            if (isDragging) {
                handleProgressClick(e);
            }
        };

        const handleMouseUp = () => {
            isDragging = false;
            document.removeEventListener('mousemove', handleMouseMove);
            document.removeEventListener('mouseup', handleMouseUp);
        };

        progressContainer.addEventListener('click', handleProgressClick);
        progressContainer.addEventListener('mousedown', (e) => {
            isDragging = true;
            handleProgressClick(e);
            document.addEventListener('mousemove', handleMouseMove);
            document.addEventListener('mouseup', handleMouseUp);
        });

        video.addEventListener('timeupdate', updateProgress);
        video.addEventListener('loadedmetadata', updateProgress);
    }

    setupControlsVisibility(videoManager) {
        const { container, customControls } = videoManager;
        let hideTimeout;

        const showControls = () => {
            customControls.classList.remove('opacity-0');
            customControls.classList.add('opacity-100');
            clearTimeout(hideTimeout);
        };

        const hideControls = () => {
            hideTimeout = setTimeout(() => {
                customControls.classList.remove('opacity-100');
                customControls.classList.add('opacity-0');
            }, 2000);
        };

        container.addEventListener('mouseenter', showControls);
        container.addEventListener('mouseleave', hideControls);
        container.addEventListener('mousemove', () => {
            showControls();
            hideControls();
        });

        // Показываем контролы при паузе
        videoManager.video.addEventListener('pause', showControls);
        videoManager.video.addEventListener('play', hideControls);
    }

    updateTimeDisplay(videoManager) {
        const { video, customControls } = videoManager;
        const timeDisplay = customControls?.querySelector('.time-display');

        if (!timeDisplay) return;

        const formatTime = (seconds) => {
            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        };

        const updateTime = () => {
            const current = formatTime(video.currentTime || 0);
            const total = formatTime(video.duration || 0);
            timeDisplay.textContent = `${current} / ${total}`;
        };

        video.addEventListener('timeupdate', updateTime);
        video.addEventListener('loadedmetadata', updateTime);
        updateTime();
    }

    // === НОВЫЙ МЕТОД ===
    initializeMuteButtonState(videoManager) {
        const { video, customControls } = videoManager;

        if (!customControls) return;

        const volumeOn = customControls.querySelector('.volume-on');
        const volumeOff = customControls.querySelector('.volume-off');

        // Проверяем изначальное состояние muted из HTML атрибута
        if (video.muted) {
            volumeOn?.classList.add('hidden');
            volumeOff?.classList.remove('hidden');
        } else {
            volumeOff?.classList.add('hidden');
            volumeOn?.classList.remove('hidden');
        }
    }

    // === НОВЫЙ МЕТОД ===
    syncMuteButtonState(videoManager) {
        const { video, customControls } = videoManager;

        if (!customControls) return;

        const volumeOn = customControls.querySelector('.volume-on');
        const volumeOff = customControls.querySelector('.volume-off');

        if (video.muted) {
            volumeOn?.classList.add('hidden');
            volumeOff?.classList.remove('hidden');
        } else {
            volumeOff?.classList.add('hidden');
            volumeOn?.classList.remove('hidden');
        }
    }

    togglePlayPause(videoManager) {
        const { video, customControls } = videoManager;
        const playIcon = customControls.querySelector('.play-icon');
        const pauseIcon = customControls.querySelector('.pause-icon');

        if (video.paused) {
            this.playVideo(videoManager);
        } else {
            this.pauseVideo(videoManager);
        }
    }

    toggleMute(videoManager) {
        const { video, customControls } = videoManager;
        const volumeOn = customControls.querySelector('.volume-on');
        const volumeOff = customControls.querySelector('.volume-off');

        video.muted = !video.muted;

        if (video.muted) {
            volumeOn.classList.add('hidden');
            volumeOff.classList.remove('hidden');
        } else {
            volumeOff.classList.add('hidden');
            volumeOn.classList.remove('hidden');
        }
    }

    toggleFullscreen(videoManager) {
        const { video } = videoManager;

        if (document.fullscreenElement) {
            document.exitFullscreen();
        } else {
            video.requestFullscreen().catch(err => {
                console.log('Error entering fullscreen:', err);
            });
        }
    }

    // === ОБНОВЛЕННЫЙ МЕТОД ===
    setupVideoHandlers(videoManager) {
        const { video, container, index } = videoManager;

        // Создаем улучшенный индикатор загрузки
        const loadingIndicator = this.createLoadingIndicator();
        container.appendChild(loadingIndicator);
        videoManager.loadingIndicator = loadingIndicator;

        let loadingTimeout;

        const hideLoadingIndicator = () => {
            loadingIndicator.classList.add('hidden');
            if (loadingTimeout) {
                clearTimeout(loadingTimeout);
            }
        };

        const showLoadingIndicator = () => {
            loadingIndicator.classList.remove('hidden');
            loadingTimeout = setTimeout(() => {
                hideLoadingIndicator();
            }, 10000);
        };

        // Обработчики событий
        video.addEventListener('loadstart', showLoadingIndicator);
        video.addEventListener('loadeddata', () => {
            hideLoadingIndicator();
            // Синхронизируем состояние кнопки mute после загрузки
            this.syncMuteButtonState(videoManager);
        });
        video.addEventListener('canplay', hideLoadingIndicator);
        video.addEventListener('canplaythrough', hideLoadingIndicator);
        video.addEventListener('error', (e) => {
            console.error(`Video ${index}: error event`, e);
            hideLoadingIndicator();
            this.showVideoError(videoManager);
        });

        video.addEventListener('play', () => {
            this.pauseOtherVideos(video);
            this.updatePlayButtonState(videoManager, true);
        });

        video.addEventListener('pause', () => {
            this.updatePlayButtonState(videoManager, false);
        });

        // === НОВЫЙ ОБРАБОТЧИК ===
        // Отслеживаем изменения состояния muted
        video.addEventListener('volumechange', () => {
            this.syncMuteButtonState(videoManager);
        });

        if (video.readyState >= 2) {
            hideLoadingIndicator();
            this.syncMuteButtonState(videoManager);
        }
    }

    createLoadingIndicator() {
        const loader = document.createElement('div');
        loader.className = 'video-loading-indicator absolute inset-0 flex items-center justify-center bg-black bg-opacity-70 text-white rounded-2xl backdrop-blur-sm';
        loader.innerHTML = `
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 border-4 border-white border-t-transparent rounded-full animate-spin mb-4"></div>
                <p class="text-sm font-medium">Loading video...</p>
            </div>
        `;
        return loader;
    }

    updatePlayButtonState(videoManager, isPlaying) {
        const { customControls } = videoManager;
        if (!customControls) return;

        const playIcon = customControls.querySelector('.play-icon');
        const pauseIcon = customControls.querySelector('.pause-icon');

        if (isPlaying) {
            playIcon?.classList.add('hidden');
            pauseIcon?.classList.remove('hidden');
        } else {
            playIcon?.classList.remove('hidden');
            pauseIcon?.classList.add('hidden');
        }
    }

    // Утилитарные методы
    playVideo(videoManager) {
        const { video, index } = videoManager;

        const playPromise = video.play();
        if (playPromise !== undefined) {
            playPromise
                .then(() => {
                    console.log(`Video ${index}: playback started successfully`);
                })
                .catch(error => {
                    console.log(`Video ${index}: autoplay blocked:`, error);
                    this.showAutoplayBlockedMessage(videoManager);
                });
        }
    }

    pauseVideo(videoManager) {
        const { video } = videoManager;
        try {
            video.pause();
        } catch (error) {
            console.log('Error pausing video:', error);
        }
    }

    pauseOtherVideos(currentVideo) {
        this.videos.forEach((videoManager, video) => {
            if (video !== currentVideo && !video.paused) {
                this.pauseVideo(videoManager);
            }
        });
    }

    setupVideoLazyLoading(videoManager) {
        const { video, index } = videoManager;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    if (video.preload === 'none') {
                        video.preload = 'metadata';
                    }
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '50px'
        });

        observer.observe(video);
    }

    showVideoError(videoManager) {
        const { container, index } = videoManager;

        const existingError = container.querySelector('.video-error-indicator');
        if (existingError) {
            existingError.remove();
        }

        const errorDiv = document.createElement('div');
        errorDiv.className = 'video-error-indicator absolute inset-0 flex items-center justify-center bg-red-50 text-red-600 rounded-2xl border-2 border-red-200';
        errorDiv.innerHTML = `
            <div class="text-center p-4">
                <svg class="w-16 h-16 mx-auto mb-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <p class="text-sm font-medium mb-2">Error loading video</p>
                <button class="px-4 py-2 bg-red-500 text-white rounded-md text-sm hover:bg-red-600 transition-colors" onclick="location.reload()">
                    Refresh page
                </button>
            </div>
        `;
        container.appendChild(errorDiv);
    }

    showAutoplayBlockedMessage(videoManager) {
        const { container } = videoManager;

        if (container.querySelector('.autoplay-blocked-message')) return;

        const message = document.createElement('div');
        message.className = 'autoplay-blocked-message absolute top-4 left-4 right-4 bg-blue-600 text-white px-4 py-3 rounded-lg text-sm z-10 flex items-center justify-between backdrop-blur-sm';
        message.innerHTML = `
            <span class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M8 5v14l11-7z"/>
                </svg>
                Click to play video
            </span>
            <button class="ml-2 text-white hover:text-gray-200 transition-colors" onclick="this.parentElement.remove()">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;

        container.appendChild(message);

        setTimeout(() => {
            if (message.parentElement) {
                message.remove();
            }
        }, 5000);
    }

    checkAutoplayCapability(videoManager) {
        const { video } = videoManager;

        if ('getAutoplayPolicy' in video) {
            const policy = video.getAutoplayPolicy();
            if (policy === 'disallowed') {
                return false;
            }
        }
        return true;
    }

    setupGlobalEventListeners() {
        // Глобальные обработчики клавиш
        document.addEventListener('keydown', (e) => {
            const focusedVideo = document.activeElement;
            if (focusedVideo && focusedVideo.tagName === 'VIDEO') {
                const videoManager = this.videos.get(focusedVideo);
                if (!videoManager) return;

                switch (e.code) {
                    case 'Space':
                        e.preventDefault();
                        this.togglePlayPause(videoManager);
                        break;
                    case 'KeyM':
                        e.preventDefault();
                        this.toggleMute(videoManager);
                        break;
                    case 'KeyF':
                        e.preventDefault();
                        this.toggleFullscreen(videoManager);
                        break;
                    case 'ArrowLeft':
                        e.preventDefault();
                        focusedVideo.currentTime = Math.max(0, focusedVideo.currentTime - 10);
                        break;
                    case 'ArrowRight':
                        e.preventDefault();
                        focusedVideo.currentTime = Math.min(focusedVideo.duration, focusedVideo.currentTime + 10);
                        break;
                }
            }
        });

        // Отслеживаем взаимодействие пользователя
        document.addEventListener('click', () => {
            if (!this.userInteracted) {
                this.userInteracted = true;
            }
        }, { once: true });
    }

    // Публичные методы API
    playAll() {
        this.videos.forEach((videoManager) => {
            if (videoManager.video.muted) {
                this.playVideo(videoManager);
            }
        });
    }

    pauseAll() {
        this.videos.forEach((videoManager) => {
            this.pauseVideo(videoManager);
        });
    }

    muteAll() {
        this.videos.forEach((videoManager) => {
            videoManager.video.muted = true;
            this.updateMuteButtonState(videoManager, true);
        });
    }

    unmuteAll() {
        this.videos.forEach((videoManager) => {
            videoManager.video.muted = false;
            this.updateMuteButtonState(videoManager, false);
        });
    }

    // === ОБНОВЛЕННЫЙ МЕТОД ===
    updateMuteButtonState(videoManager, isMuted) {
        const { customControls } = videoManager;
        if (!customControls) return;

        const volumeOn = customControls.querySelector('.volume-on');
        const volumeOff = customControls.querySelector('.volume-off');

        if (isMuted) {
            volumeOn?.classList.add('hidden');
            volumeOff?.classList.remove('hidden');
        } else {
            volumeOff?.classList.add('hidden');
            volumeOn?.classList.remove('hidden');
        }
    }

    getVideoInfo() {
        const info = Array.from(this.videos.values()).map((videoManager) => ({
            index: videoManager.index,
            src: videoManager.video.src,
            readyState: videoManager.video.readyState,
            paused: videoManager.video.paused,
            muted: videoManager.video.muted,
            autoplay: videoManager.video.hasAttribute('autoplay'),
            controls: videoManager.video.hasAttribute('controls'),
            currentTime: videoManager.video.currentTime,
            duration: videoManager.video.duration
        }));

        console.table(info);
        return info;
    }
}

// Инициализация
const videoManager = new AssetOverviewVideoManager();

// Экспорт в глобальную область видимости для обратной совместимости
window.AssetOverviewVideo = {
    playAll: () => videoManager.playAll(),
    pauseAll: () => videoManager.pauseAll(),
    muteAll: () => videoManager.muteAll(),
    unmuteAll: () => videoManager.unmuteAll(),
    getVideoInfo: () => videoManager.getVideoInfo(),
    playVideo: (videoElement) => {
        const videoManagerInstance = videoManager.videos.get(videoElement);
        if (videoManagerInstance) {
            videoManager.playVideo(videoManagerInstance);
        }
    }
};

// Утилитарная функция для ручного скрытия индикаторов загрузки
window.hideAllLoadingIndicators = () => {
    const indicators = document.querySelectorAll('.video-loading-indicator');
    indicators.forEach(indicator => {
        indicator.classList.add('hidden');
    });
};
