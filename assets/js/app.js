/* =====================================================
   MOVIEVERSE — Global JS (Vanilla, no libraries)
   ===================================================== */

document.addEventListener('DOMContentLoaded', () => {

    // ── NAVBAR: scroll class ──────────────────────────
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        }, { passive: true });
    }

    // ── SEARCH TOGGLE ─────────────────────────────────
    const searchToggle = document.getElementById('searchToggle');
    const searchBar    = document.getElementById('searchBar');
    if (searchToggle && searchBar) {
        searchToggle.addEventListener('click', () => {
            searchBar.classList.toggle('open');
            if (searchBar.classList.contains('open')) {
                searchBar.querySelector('input')?.focus();
            }
        });
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') searchBar.classList.remove('open');
        });
    }

    // ── USER DROPDOWN ─────────────────────────────────
    const userDropdown = document.getElementById('userDropdown');
    if (userDropdown) {
        const userToggle = document.getElementById('userToggle');
        userToggle?.addEventListener('click', e => {
            e.stopPropagation();
            userDropdown.classList.toggle('open');
        });
        document.addEventListener('click', () => userDropdown.classList.remove('open'));
    }

    // ── HAMBURGER / MOBILE MENU ───────────────────────
    const hamburger  = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');
    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
            hamburger.classList.toggle('active');
        });
    }

    // ── AUTO-DISMISS FLASH ─────────────────────────────
    const flash = document.getElementById('flashMsg');
    if (flash) setTimeout(() => flash.remove(), 5000);

    // ── HORIZONTAL SCROLL ROWS ────────────────────────
    document.querySelectorAll('.scroll-row-wrap').forEach(wrap => {
        const row      = wrap.querySelector('.scroll-row');
        const btnLeft  = wrap.querySelector('.scroll-btn-left');
        const btnRight = wrap.querySelector('.scroll-btn-right');
        if (!row) return;

        const scroll = dir => { row.scrollBy({ left: dir * 500, behavior: 'smooth' }); };
        btnLeft?.addEventListener('click',  () => scroll(-1));
        btnRight?.addEventListener('click', () => scroll(1));

        const updateBtns = () => {
            if (btnLeft)  btnLeft.style.display  = row.scrollLeft < 10 ? 'none' : '';
            if (btnRight) btnRight.style.display = (row.scrollLeft + row.clientWidth >= row.scrollWidth - 10) ? 'none' : '';
        };
        row.addEventListener('scroll', updateBtns, { passive: true });
        updateBtns();
    });

    // ── STAR RATING INPUT ──────────────────────────────
    const starInputs = document.querySelectorAll('.star-rating-input input');
    const ratingVal  = document.getElementById('ratingValue');
    starInputs.forEach(input => {
        input.addEventListener('change', () => {
            if (ratingVal) ratingVal.textContent = input.value;
        });
    });
    // Set initial value
    const checked = document.querySelector('.star-rating-input input:checked');
    if (checked && ratingVal) ratingVal.textContent = checked.value;

    // ── RATING DISTRIBUTION BARS ──────────────────────
    document.querySelectorAll('[data-bar-width]').forEach(bar => {
        const w = bar.dataset.barWidth;
        requestAnimationFrame(() => { bar.style.width = w + '%'; });
    });

    // ── ANIMATE STAT NUMBERS ──────────────────────────
    document.querySelectorAll('[data-count-to]').forEach(el => {
        const target = parseInt(el.dataset.countTo);
        const duration = 1200;
        const start = performance.now();
        const animate = (now) => {
            const t = Math.min((now - start) / duration, 1);
            const ease = 1 - Math.pow(1 - t, 3);
            el.textContent = Math.round(ease * target).toLocaleString();
            if (t < 1) requestAnimationFrame(animate);
        };
        const observer = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting) {
                observer.disconnect();
                requestAnimationFrame(animate);
            }
        });
        observer.observe(el);
    });

    // ── FAVORITE TOGGLE (AJAX) ────────────────────────
    const favBtn = document.getElementById('favBtn');
    if (favBtn) {
        favBtn.addEventListener('click', async () => {
            const movieId = favBtn.dataset.movieId;
            favBtn.disabled = true;
            try {
                const res = await fetch('/api/favorite.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `movie_id=${movieId}&csrf_token=${encodeURIComponent(document.getElementById('csrfToken')?.value || '')}`
                });
                const data = await res.json();
                if (data.success) {
                    favBtn.classList.toggle('active', data.favorited);
                    const label = favBtn.querySelector('.fav-label');
                    if (label) label.textContent = data.favorited ? 'Favorited' : 'Add to Favorites';
                    showToast(data.favorited ? '❤️ Added to favorites' : '💔 Removed from favorites', 'success');
                }
            } catch {
                showToast('Something went wrong', 'error');
            } finally {
                favBtn.disabled = false;
            }
        });
    }

    // ── REVIEW DELETE (AJAX) ──────────────────────────
    document.querySelectorAll('[data-delete-review]').forEach(btn => {
        btn.addEventListener('click', async () => {
            if (!confirm('Delete this review?')) return;
            const reviewId = btn.dataset.deleteReview;
            try {
                const res = await fetch('/api/review.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=delete&review_id=${reviewId}&csrf_token=${encodeURIComponent(document.getElementById('csrfToken')?.value || '')}`
                });
                const data = await res.json();
                if (data.success) {
                    const card = btn.closest('.review-card');
                    card?.remove();
                    showToast('Review deleted', 'success');
                    const count = document.getElementById('reviewCount');
                    if (count) count.textContent = parseInt(count.textContent) - 1;
                } else {
                    showToast(data.message || 'Error', 'error');
                }
            } catch {
                showToast('Something went wrong', 'error');
            }
        });
    });

    // ── REVIEW EDIT TOGGLE ────────────────────────────
    document.querySelectorAll('[data-edit-review]').forEach(btn => {
        btn.addEventListener('click', () => {
            const reviewId = btn.dataset.editReview;
            const editForm = document.getElementById(`editForm-${reviewId}`);
            const textEl   = document.getElementById(`reviewText-${reviewId}`);
            if (editForm && textEl) {
                editForm.classList.toggle('hidden');
                textEl.classList.toggle('hidden');
                btn.textContent = editForm.classList.contains('hidden') ? 'Edit' : 'Cancel';
            }
        });
    });

    // ── FILTER / SEARCH (movies.php live filter) ──────
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                updateFilters();
            }, 400);
        });
    }

    function updateFilters() {
        const form = document.getElementById('filterForm');
        if (form) form.submit();
    }

    // ── GENRE CHIPS TOGGLE ────────────────────────────
    document.querySelectorAll('.genre-chip[data-genre]').forEach(chip => {
        chip.addEventListener('click', () => {
            const genreInput = document.getElementById('genreInput');
            if (genreInput) {
                genreInput.value = chip.classList.contains('active') ? '' : chip.dataset.genre;
                document.getElementById('filterForm')?.submit();
            }
        });
    });

    // ── SORT CHANGE ───────────────────────────────────
    const sortSelect = document.getElementById('sortSelect');
    sortSelect?.addEventListener('change', () => {
        document.getElementById('filterForm')?.submit();
    });

    // ── MODAL ─────────────────────────────────────────
    window.openModal = (id) => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.style.display = 'flex';
            requestAnimationFrame(() => modal.classList.add('open'));
        }
    };
    window.closeModal = (id) => {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('open');
            setTimeout(() => modal.style.display = 'none', 300);
        }
    };
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', e => {
            if (e.target === modal) closeModal(modal.id);
        });
    });

    // ── TOAST NOTIFICATION ───────────────────────────
    window.showToast = (message, type = 'info') => {
        const toast = document.createElement('div');
        toast.className = `flash flash-${type}`;
        toast.style.cssText = 'position:fixed;top:80px;right:16px;z-index:9999;animation:slideInRight .3s ease';
        toast.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" class="flash-close">✕</button>`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3500);
    };

    // ── CONFIRM DELETE FORMS ──────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', e => {
            if (!confirm(el.dataset.confirm || 'Are you sure?')) e.preventDefault();
        });
    });

    // ── HIDDEN CLASS HELPER ───────────────────────────
    const style = document.createElement('style');
    style.textContent = '.hidden{display:none!important}';
    document.head.appendChild(style);

    // ── ADMIN: IMAGE PREVIEW ─────────────────────────
    const posterInput = document.getElementById('posterUrlInput');
    const posterPreview = document.getElementById('posterPreview');
    if (posterInput && posterPreview) {
        const update = () => {
            const url = posterInput.value.trim();
            posterPreview.src = url || '';
            posterPreview.style.display = url ? 'block' : 'none';
        };
        posterInput.addEventListener('input', update);
        update();
    }

});
