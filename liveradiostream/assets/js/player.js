let hlsInstance = null; // HLS yayınlar için global değişken

document.addEventListener('DOMContentLoaded', () => {
    const audio = document.getElementById('main-audio-element');
    const playBtn = document.getElementById('player-play-btn');
    const playerTitle = document.getElementById('player-title');
    const playerStatus = document.getElementById('player-status');
    const playerImg = document.getElementById('player-img');
    const volumeSlider = document.getElementById('player-volume-slider');

    let currentStream = '';
    let currentLink = ''; 
    let globalLock = false;

    const userId = typeof RadioApp !== 'undefined' && RadioApp.userId ? RadioApp.userId : 'guest';
    const FAV_KEY = `radio_favs_${userId}`;
    const HIST_KEY = `radio_history_${userId}`;

    let favorites = JSON.parse(localStorage.getItem(FAV_KEY) || localStorage.getItem('radio_favs') || '[]');
    let history = JSON.parse(localStorage.getItem(HIST_KEY) || localStorage.getItem('radio_history') || '[]');

    updateFavUI();
    updateHistoryUI();
    checkSinglePageFavoriteState();
    checkSinglePagePlayState();

    function cleanTitle(str) {
        if (!str) return 'Radio Station';
        return str
            .replace(/^Listen to\s+/i, '')
            .replace(/\s+Live(\s+Stream)?$/i, '')
            .replace(/\s+Live$/i, '')
            .trim();
    }

    function sanitizeHistory() {
        const uniqueMap = new Map();
        history.forEach(item => {
            const t = cleanTitle(item.title).toLowerCase();
            if (!uniqueMap.has(t)) {
                uniqueMap.set(t, item);
            }
        });
        history = Array.from(uniqueMap.values());
        if (history.length > 10) history = history.slice(0, 10);
        localStorage.setItem(HIST_KEY, JSON.stringify(history));
    }

    function addToHistory(station) {
        if (!station || !station.title || globalLock) return;
        
        globalLock = true;
        setTimeout(() => { globalLock = false; }, 1000);

        const cleanedTitle = cleanTitle(station.title);
        history = history.filter(h => cleanTitle(h.title).toLowerCase() !== cleanedTitle.toLowerCase());
        
        station.title = cleanedTitle;
        history.unshift(station);
        
        sanitizeHistory();
        updateHistoryUI();
    }

    function incrementPlayCount(postId) {
        if (!postId || postId === 'guest') return;
        fetch(`${RadioApp.ajaxurl}?action=radio_increment_play`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `post_id=${postId}`
        }).catch(() => {});
    }

    // RANDOM PLAY ÖZELLİĞİ
    document.addEventListener('click', (e) => {
        const randomTrigger = e.target.closest('a[href*="random-play"]');
        if (randomTrigger) {
            e.preventDefault();
            e.stopPropagation();

            if (playerStatus) playerStatus.textContent = 'Selecting Random Station...';

            fetch(`${RadioApp.ajaxurl}?action=radio_random_play`)
                .then(res => res.json())
                .then(response => {
                    if (response.success && response.data) {
                        const station = response.data;
                        if (station.stream) {
                            playStream(station.stream, cleanTitle(station.title), station.img, station.id, station.link);
                            addToHistory({ id: station.id, title: station.title, link: station.link });
                        } else {
                            alert('Selected random station stream is unavailable.');
                            if (playerStatus) playerStatus.textContent = 'Stream Offline / Failed';
                        }
                    } else {
                        alert('No radio stations available for random play.');
                        if (playerStatus) playerStatus.textContent = 'Failed';
                    }
                })
                .catch(() => {
                    alert('Connection error while fetching random station.');
                    if (playerStatus) playerStatus.textContent = 'Failed';
                });
            return;
        }

        if (e.target.closest('#live-search-results') || e.target.closest('#live-search-input')) {
            return;
        }

        const favBtn = e.target.closest('.btn-favorite, .js-fav-toggle, .btn-fav-green');
        if (favBtn) {
            e.preventDefault();
            e.stopPropagation();

            let rawTitle = document.querySelector('h1')?.textContent || favBtn.getAttribute('data-title');
            let title = cleanTitle(rawTitle);
            let link = window.location.href;
            let img = document.querySelector('.single-radio-header img, img.player-thumb')?.src || '';
            let id = favBtn.getAttribute('data-id') || link;

            let existingIdx = favorites.findIndex(f => f.title.toLowerCase() === title.toLowerCase());
            if (existingIdx > -1) {
                favorites.splice(existingIdx, 1);
            } else {
                if (favorites.length < 50) {
                    favorites.push({ id, title, link, img });
                } else {
                    alert('You can add a maximum of 50 favorite stations.');
                }
            }

            localStorage.setItem(FAV_KEY, JSON.stringify(favorites));
            updateFavUI();
            checkSinglePageFavoriteState();
            return;
        }

        const embedBtn = e.target.closest('#embed-player-btn, .btn-embed-yellow');
        if (embedBtn) {
            e.preventDefault();
            e.stopPropagation();
            const embedBox = document.getElementById('embed-code-box');
            if (embedBox) {
                embedBox.style.display = embedBox.style.display === 'block' ? 'none' : 'block';
            }
            return;
        }

        // KIRIK YAYIN BİLDİRİMİ
        const reportBtn = e.target.closest('#report-broken-btn, #report-issue-btn');
        if (reportBtn) {
            e.preventDefault();
            e.stopPropagation();

            const radioId = reportBtn.getAttribute('data-id');
            if (!radioId) return;

            reportBtn.disabled = true;
            reportBtn.innerText = 'Reporting...';

            const ajaxUrl = (typeof RadioApp !== 'undefined' && RadioApp.ajaxurl) ? RadioApp.ajaxurl : '/wp-admin/admin-ajax.php';

            fetch(ajaxUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=report_broken_radio&radio_id=' + radioId
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    reportBtn.innerText = 'Reported Successfully!';
                    reportBtn.style.background = '#10b981';
                    alert("Thank you! The broken stream report has been sent to our moderators.");
                } else {
                    reportBtn.innerText = 'Error!';
                    reportBtn.disabled = false;
                    alert("Failed to report stream. Please try again.");
                }
            })
            .catch(err => {
                console.error('Report error:', err);
                reportBtn.innerText = 'Failed';
                reportBtn.disabled = false;
            });
            return;
        }

        const removeFavBtn = e.target.closest('.btn-remove-fav');
        if (removeFavBtn) {
            e.preventDefault();
            e.stopPropagation();
            const id = removeFavBtn.dataset.id;
            favorites = favorites.filter(f => f.id !== id);
            localStorage.setItem(FAV_KEY, JSON.stringify(favorites));
            updateFavUI();
            checkSinglePageFavoriteState();
            return;
        }

        const removeHistBtn = e.target.closest('.btn-remove-hist');
        if (removeHistBtn) {
            e.preventDefault();
            e.stopPropagation();
            const id = removeHistBtn.dataset.id;
            history = history.filter(h => h.id !== id);
            localStorage.setItem(HIST_KEY, JSON.stringify(history));
            updateHistoryUI();
            return;
        }

        const playTrigger = e.target.closest('.js-play-trigger, .btn-mini-play, .btn-play-single, .btn-play-hero');
        if (!playTrigger) return;

        e.preventDefault();
        e.stopPropagation();

        let card = playTrigger.closest('.radio-card') || playTrigger.parentElement;
        let stream = playTrigger.getAttribute('data-stream') || card?.dataset.stream || document.getElementById('single-radio-hero')?.dataset.stream;
        let rawTitle = playTrigger.getAttribute('data-title') || card?.querySelector('h3, .radio-title, h1')?.textContent.trim();
        let title = cleanTitle(rawTitle);
        let img = playTrigger.getAttribute('data-img') || card?.querySelector('img')?.src;
        let link = playTrigger.getAttribute('data-link') || card?.querySelector('a')?.href;
        let id = playTrigger.getAttribute('data-id') || link || title;

        if (stream) {
            playStream(stream, title, img || '', id, link);
            addToHistory({ id, title, link: link || '#' });
        } else if (link) {
            fetchStreamAndPlay(link, title, img, id);
        }
    }, true);

    function fetchStreamAndPlay(link, title, img, id) {
        if (playerStatus) playerStatus.textContent = 'Fetching Stream...';
        fetch(link)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const hero = doc.getElementById('single-radio-hero');
                const fetchedStream = hero ? hero.dataset.stream : doc.querySelector('audio')?.src;
                
                if (fetchedStream) {
                    playStream(fetchedStream, title, img, id, link);
                    addToHistory({ id: id || link, title, link });
                } else {
                    alert('Stream URL could not be retrieved for this station!');
                    if (playerStatus) playerStatus.textContent = 'Stream Offline / Failed';
                }
            })
            .catch(() => {
                alert('Stream connection failed!');
                if (playerStatus) playerStatus.textContent = 'Stream Offline / Failed';
            });
    }

    function playStream(streamUrl, title, imgUrl, stationId, stationLink) {
        if (!streamUrl) {
            alert('Stream URL is not available for this station!');
            return;
        }

        // Önceki aktif HLS oturumunu temizleyelim
        if (hlsInstance) {
            hlsInstance.destroy();
            hlsInstance = null;
        }

        // 1. ÖNCE .m3u8 (HLS) YAYINLARI YAKALAYALIM
        if (streamUrl.includes('.m3u8')) {
            currentStream = streamUrl;
            currentLink = stationLink || window.location.href; 

            if (playerTitle) {
                playerTitle.textContent = title;
                playerTitle.style.cursor = 'pointer';
                playerTitle.onclick = () => { if (currentLink && currentLink !== '#') window.location.href = currentLink; };
            }
            if (playerStatus) playerStatus.textContent = 'Connecting Live Stream...';
            if (playerImg && imgUrl) playerImg.src = imgUrl;

            if (typeof Hls !== 'undefined' && Hls.isSupported()) {
                hlsInstance = new Hls();
                hlsInstance.loadSource(streamUrl);
                hlsInstance.attachMedia(audio);
                hlsInstance.on(Hls.Events.MANIFEST_PARSED, () => {
                    audio.play().then(() => {
                        updatePlayState(true);
                        if (playerStatus) playerStatus.textContent = 'Live Broadcasting';
                        if (stationId) incrementPlayCount(stationId);
                    }).catch(err => {
                        console.error("HLS Playback error:", err);
                        if (playerStatus) playerStatus.textContent = 'Stream Offline / Failed';
                        updatePlayState(false);
                    });
                });
                hlsInstance.on(Hls.Events.ERROR, function(event, data) {
                    if (data.fatal) {
                        console.error("HLS fatal error:", data);
                        if (playerStatus) playerStatus.textContent = 'Stream Offline / Failed';
                        updatePlayState(false);
                    }
                });
                return;
            } else if (audio.canPlayType('application/vnd.apple.mpegurl')) {
                audio.src = streamUrl;
                audio.play().then(() => {
                    updatePlayState(true);
                    if (playerStatus) playerStatus.textContent = 'Live Broadcasting';
                }).catch(() => {
                    updatePlayState(false);
                });
                return;
            } else {
                alert('Your browser does not support HLS streams.');
                if (playerStatus) playerStatus.textContent = 'Unsupported Format / Failed';
                return;
            }
        }

        // 2. SADECE .m3u8 OLMAYAN DİĞER .pls VE SADECE .m3u DOSYALARINI ENGELLEYELİM
        if (streamUrl.includes('.pls') || (streamUrl.includes('.m3u') && !streamUrl.includes('.m3u8'))) {
            alert('This station uses an unsupported playlist format.');
            if (playerStatus) playerStatus.textContent = 'Unsupported Format / Failed';
            return;
        }

        if (currentStream === streamUrl && !audio.paused) {
            audio.pause();
            updatePlayState(false);
            return;
        }

        currentStream = streamUrl;
        currentLink = stationLink || window.location.href; 
        
        if (playerTitle) {
            playerTitle.textContent = title;
            playerTitle.style.cursor = 'pointer';
            playerTitle.title = 'Click to view station details';
            playerTitle.onclick = () => {
                if (currentLink && currentLink !== '#') {
                    window.location.href = currentLink;
                }
            };
        }
        
        if (playerStatus) playerStatus.textContent = 'Connecting Live Stream...';
        if (playerImg && imgUrl) playerImg.src = imgUrl;

        // Standard MP3 / AAC Streams
        audio.src = streamUrl;
        audio.load();

        audio.play().then(() => {
            updatePlayState(true);
            if (playerStatus) playerStatus.textContent = 'Live Broadcasting';
            if (stationId) incrementPlayCount(stationId);
        }).catch(err => {
            console.error("Playback error:", err);
            if (playerStatus) playerStatus.textContent = 'Stream Offline / Failed';
            updatePlayState(false);
        });
    }

    if (playBtn) {
        playBtn.addEventListener('click', () => {
            if (!audio.src && !currentStream.includes('.m3u8')) return;
            if (audio.paused) {
                audio.play().catch(() => {});
                updatePlayState(true);
                if (playerStatus) playerStatus.textContent = 'Live Broadcasting';
            } else {
                audio.pause();
                updatePlayState(false);
                if (playerStatus) playerStatus.textContent = 'Paused';
            }
        });
    }

    if (volumeSlider) {
        volumeSlider.addEventListener('input', (e) => {
            audio.volume = e.target.value;
        });
    }

    function updatePlayState(isPlaying) {
        if (playBtn) {
            playBtn.textContent = isPlaying ? '||' : '>';
        }

        const heroPlayBtn = document.querySelector('.btn-play-hero');
        if (heroPlayBtn) {
            if (isPlaying) {
                heroPlayBtn.innerHTML = '<svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 6px;"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg> Pause Stream';
                heroPlayBtn.style.background = '#c2410c';
            } else {
                heroPlayBtn.innerHTML = '<svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 6px;"><path d="M8 5v14l11-7z"/></svg> Play Live Stream';
                heroPlayBtn.style.background = '';
            }
        }
    }

    function checkSinglePagePlayState() {
        const heroPlayBtn = document.querySelector('.btn-play-hero');
        if (!heroPlayBtn) return;

        let currentHeroStream = document.getElementById('single-radio-hero')?.dataset.stream;
        if (currentStream === currentHeroStream && !audio.paused) {
            heroPlayBtn.innerHTML = '<svg width="14" height="14" fill="currentColor" viewBox="0 0 24 24" style="vertical-align: middle; margin-right: 6px;"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg> Pause Stream';
            heroPlayBtn.style.background = '#c2410c';
        }
    }

    function updateFavUI() {
        const favList = document.getElementById('favorites-list');
        const favCount = document.getElementById('fav-count');
        if (favCount) favCount.textContent = favorites.length;

        if (favList) {
            if (favorites.length === 0) {
                favList.innerHTML = '<li style="color: #94a3b8; font-size: 0.85rem;">No favorite stations added yet.</li>';
                return;
            }
            favList.innerHTML = favorites.map(f => `
                <li class="fav-item-row" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <a href="${f.link}" style="color:inherit; text-decoration:none;">${f.title}</a>
                    <button type="button" class="btn-remove-item btn-remove-fav" data-id="${f.id}" title="Remove" style="background:none; border:none; color:#ef4444; cursor:pointer; font-weight:bold;">X</button>
                </li>
            `).join('');
        }
    }

    function updateHistoryUI() {
        sanitizeHistory();
        const histList = document.getElementById('history-list');
        if (histList) {
            if (history.length === 0) {
                histList.innerHTML = '<li style="color: #94a3b8; font-size: 0.85rem;">Your listening history will appear here.</li>';
                return;
            }
            histList.innerHTML = history.map(h => `
                <li class="hist-item-row" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <a href="${h.link}" style="color:inherit; text-decoration:none;">${h.title}</a>
                    <button type="button" class="btn-remove-item btn-remove-hist" data-id="${h.id}" title="Remove" style="background:none; border:none; color:#ef4444; cursor:pointer; font-weight:bold;">X</button>
                </li>
            `).join('');
        }
    }

    function checkSinglePageFavoriteState() {
        const favBtn = document.querySelector('.btn-favorite, .js-fav-toggle, .btn-fav-green');
        if (!favBtn) return;

        let title = cleanTitle(document.querySelector('h1')?.textContent);
        let isFav = favorites.some(f => f.title.toLowerCase() === title.toLowerCase());

        favBtn.innerHTML = isFav ? '✔ Added to Favorite' : 'Add Favorite';
    }

    const searchInput = document.getElementById('live-search-input');
    const searchResults = document.getElementById('live-search-results');
    const clearSearchBtn = document.getElementById('clear-search-btn');

    if (searchInput && searchResults) {
        let searchDebounce;
        searchInput.addEventListener('input', (e) => {
            const val = e.target.value.trim();
            if (val.length > 0) {
                if (clearSearchBtn) clearSearchBtn.style.display = 'block';
            } else {
                if (clearSearchBtn) clearSearchBtn.style.display = 'none';
                searchResults.style.display = 'none';
                searchResults.innerHTML = '';
                return;
            }

            if (val.length < 1) return;

            clearTimeout(searchDebounce);
            searchDebounce = setTimeout(() => {
                fetch(`${RadioApp.ajaxurl}?action=radio_live_search&term=${encodeURIComponent(val)}`)
                    .then(res => res.text())
                    .then(text => {
                        try {
                            const res = JSON.parse(text);
                            if (res.success && res.data && res.data.length > 0) {
                                searchResults.innerHTML = res.data.map(item => `
                                    <div class="search-ajax-item" data-stream="${item.stream}" data-title="${item.title}" data-img="${item.thumb}" data-link="${item.link}" data-id="${item.id}" style="display:flex; align-items:center; justify-content:space-between; padding:10px; border-bottom:1px solid #334155; cursor:pointer;">
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <img src="${item.thumb}" alt="${item.title}" style="width:40px; height:40px; object-fit:cover; border-radius:4px;">
                                            <div>
                                                <span style="color:#fff; font-weight:700; font-size:0.9rem;">${item.title}</span><br>
                                                <small style="color: #38bdf8; font-size:0.75rem;">Click to play live</small>
                                            </div>
                                        </div>
                                        <span style="background:#10b981; color:#fff; padding:6px 12px; border-radius:4px; font-size:0.8rem; font-weight:bold;">Play</span>
                                    </div>
                                `).join('');
                                searchResults.style.display = 'block';
                            } else {
                                searchResults.innerHTML = '<div style="padding:1rem; text-align:center; color:#94a3b8; font-size:0.85rem;">No radio station found matching your query.</div>';
                                searchResults.style.display = 'block';
                            }
                        } catch(err) {
                            console.error('JSON Parse error:', err);
                        }
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                    });
            }, 300);
        });

        searchResults.addEventListener('click', (e) => {
            const item = e.target.closest('.search-ajax-item');
            if (!item) return;

            e.stopImmediatePropagation();
            e.preventDefault();

            const stream = item.dataset.stream;
            const title = cleanTitle(item.dataset.title);
            const img = item.dataset.img;
            const link = item.dataset.link;
            const id = item.dataset.id || title;

            searchResults.style.display = 'none';
            if (searchInput) searchInput.value = '';

            if (stream) {
                playStream(stream, title, img, id, link);
                addToHistory({ id, title, link });
            } else if (link) {
                fetchStreamAndPlay(link, title, img, id);
            }
        });

        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', () => {
                searchInput.value = '';
                clearSearchBtn.style.display = 'none';
                searchResults.style.display = 'none';
                searchResults.innerHTML = '';
                searchInput.focus();
            });
        }

        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }

    // OYLAMA MODALI
    const starRatingBtn = document.getElementById('star-rating-btn');
    if (starRatingBtn) {
        let modal = document.getElementById('custom-rating-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'custom-rating-modal';
            modal.style.cssText = 'display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:9999; justify-content:center; align-items:center; backdrop-filter: blur(4px);';
            modal.innerHTML = `
                <div style="background:#111827; border:1px solid #1e293b; padding:2rem; border-radius:16px; width:320px; text-align:center; box-shadow:0 20px 25px -5px rgba(0,0,0,0.5); color:#fff; font-family:inherit;">
                    <h3 style="margin-top:0; font-size:1.2rem; color:#f8fafc; margin-bottom:0.5rem;">Rate This Station</h3>
                    <p style="color:#94a3b8; font-size:0.85rem; margin-bottom:1.5rem;">Select your rating from 1 to 5 stars:</p>
                    <div id="modal-stars-select" style="font-size:1.8rem; cursor:pointer; margin-bottom:1.5rem; letter-spacing:4px;">
                        <span data-value="1" style="color:#64748b; transition:color 0.2s;">&#9733;</span>
                        <span data-value="2" style="color:#64748b; transition:color 0.2s;">&#9733;</span>
                        <span data-value="3" style="color:#64748b; transition:color 0.2s;">&#9733;</span>
                        <span data-value="4" style="color:#64748b; transition:color 0.2s;">&#9733;</span>
                        <span data-value="5" style="color:#64748b; transition:color 0.2s;">&#9733;</span>
                    </div>
                    <div id="modal-msg-area" style="display:none; margin-bottom:1rem; padding:8px; border-radius:6px; font-size:0.85rem; font-weight:600;"></div>
                    <div style="display:flex; gap:10px;" id="modal-buttons-area">
                        <button id="modal-submit-rating" style="flex:1; background:#f97316; color:#fff; border:none; padding:10px; border-radius:8px; font-weight:700; cursor:pointer; font-size:0.9rem;">Submit Vote</button>
                        <button id="modal-close-rating" style="flex:1; background:#1e293b; color:#94a3b8; border:none; padding:10px; border-radius:8px; font-weight:600; cursor:pointer; font-size:0.9rem;">Cancel</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        let selectedRatingValue = 5;
        const starSpans = modal.querySelectorAll('#modal-stars-select span');
        const msgArea = modal.querySelector('#modal-msg-area');
        const buttonsArea = modal.querySelector('#modal-buttons-area');

        starSpans.forEach((span, idx) => {
            span.addEventListener('mouseover', () => {
                starSpans.forEach((s, i) => {
                    s.style.color = i <= idx ? '#f59e0b' : '#64748b';
                });
            });
            span.addEventListener('click', () => {
                selectedRatingValue = parseInt(span.dataset.value);
                starSpans.forEach((s, i) => {
                    s.style.color = i < selectedRatingValue ? '#f59e0b' : '#64748b';
                });
            });
        });

        modal.querySelector('#modal-stars-select').addEventListener('mouseleave', () => {
            starSpans.forEach((s, i) => {
                s.style.color = i < selectedRatingValue ? '#f59e0b' : '#64748b';
            });
        });

        starRatingBtn.addEventListener('click', () => {
            selectedRatingValue = 5;
            starSpans.forEach((s, i) => {
                s.style.color = i < 5 ? '#f59e0b' : '#64748b';
            });
            msgArea.style.display = 'none';
            buttonsArea.style.display = 'flex';
            modal.style.display = 'flex';
        });

        modal.querySelector('#modal-close-rating').addEventListener('click', () => {
            modal.style.display = 'none';
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });

        modal.querySelector('#modal-submit-rating').addEventListener('click', () => {
            const postId = starRatingBtn.dataset.id;
            
            fetch(`${RadioApp.ajaxurl}?action=radio_rate_station`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `post_id=${postId}&rating=${selectedRatingValue}`
            })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    document.getElementById('rating-val-display').textContent = response.data.rating_avg;
                    document.getElementById('votes-val-display').textContent = response.data.votes_count;
                    
                    msgArea.textContent = '✔ Thank you! Your vote has been recorded.';
                    msgArea.style.background = 'rgba(16, 185, 129, 0.15)';
                    msgArea.style.color = '#10b981';
                    msgArea.style.border = '1px solid #10b981';
                    msgArea.style.display = 'block';
                    buttonsArea.style.display = 'none';

                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 1500);
                } else {
                    msgArea.textContent = 'Failed to submit rating. Try again.';
                    msgArea.style.background = 'rgba(239, 68, 68, 0.15)';
                    msgArea.style.color = '#ef4444';
                    msgArea.style.border = '1px solid #ef4444';
                    msgArea.style.display = 'block';
                }
            })
            .catch(() => {
                msgArea.textContent = 'Connection error occurred.';
                msgArea.style.background = 'rgba(239, 68, 68, 0.15)';
                msgArea.style.color = '#ef4444';
                msgArea.style.border = '1px solid #ef4444';
                msgArea.style.display = 'block';
            });
        });
    }
});