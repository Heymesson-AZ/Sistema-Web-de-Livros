/**
 * Carrossel de Livros em Vanilla JavaScript
 * Zero dependências externas (sem jQuery, sem Slick)
 * Suporte a touch swipe mobile, mouse drag, autoplay e responsividade completa.
 */
export function initCarrosel() {
    const carrosselContainers = document.querySelectorAll(
        ".livros-carousel-container",
    );

    carrosselContainers.forEach((container) => {
        const track = container.querySelector(".carousel-track");
        const slides = container.querySelectorAll(".carousel-slide");
        const prevBtn = container.querySelector(".carousel-btn-prev");
        const nextBtn = container.querySelector(".carousel-btn-next");
        const dotsContainer = container.querySelector(".carousel-dots");

        if (!track || slides.length === 0) return;

        let currentIndex = 0;
        let startX = 0;
        let currentTranslate = 0;
        let prevTranslate = 0;
        let isDragging = false;
        let autoplayTimer = null;
        const totalSlides = slides.length;

        // Determina quantos slides exibir por tela
        function getSlidesPerView() {
            const width = window.innerWidth;
            if (width < 576) return 1;
            if (width < 992) return 2;
            if (width < 1200) return 3;
            return 4;
        }

        function getMaxIndex() {
            const perView = getSlidesPerView();
            return Math.max(0, totalSlides - perView);
        }

        // Cria os pontos de navegação (dots)
        function renderDots() {
            if (!dotsContainer) return;
            dotsContainer.innerHTML = "";
            const maxIndex = getMaxIndex();
            const numDots = maxIndex + 1;

            for (let i = 0; i < numDots; i++) {
                const dot = document.createElement("button");
                dot.type = "button";
                dot.className = `carousel-dot ${i === currentIndex ? "active" : ""}`;
                dot.setAttribute("aria-label", `Ir para slide ${i + 1}`);
                dot.addEventListener("click", () => goToSlide(i));
                dotsContainer.appendChild(dot);
            }
        }

        // Atualiza a posição do track e classes ativas
        function updatePosition() {
            const perView = getSlidesPerView();
            const slideWidthPercent = 100 / perView;
            const translatePercent = -(currentIndex * slideWidthPercent);

            track.style.transform = `translateX(${translatePercent}%)`;

            // Atualiza botões
            if (prevBtn) {
                prevBtn.disabled = currentIndex === 0;
                prevBtn.classList.toggle("disabled", currentIndex === 0);
            }
            if (nextBtn) {
                const maxIndex = getMaxIndex();
                nextBtn.disabled = currentIndex >= maxIndex;
                nextBtn.classList.toggle("disabled", currentIndex >= maxIndex);
            }

            // Atualiza dots
            if (dotsContainer) {
                const dots = dotsContainer.querySelectorAll(".carousel-dot");
                dots.forEach((dot, idx) => {
                    dot.classList.toggle("active", idx === currentIndex);
                });
            }

            // Atualiza classes ativas nos slides
            slides.forEach((slide, idx) => {
                const isVisible =
                    idx >= currentIndex && idx < currentIndex + perView;
                slide.classList.toggle("is-visible", isVisible);
                slide.classList.toggle("is-active", idx === currentIndex);
            });
        }

        function goToSlide(index) {
            const maxIndex = getMaxIndex();
            currentIndex = Math.max(0, Math.min(index, maxIndex));
            updatePosition();
        }

        function nextSlide() {
            const maxIndex = getMaxIndex();
            if (currentIndex >= maxIndex) {
                currentIndex = 0; // Loop infinito suave
            } else {
                currentIndex++;
            }
            updatePosition();
        }

        function prevSlide() {
            const maxIndex = getMaxIndex();
            if (currentIndex <= 0) {
                currentIndex = maxIndex;
            } else {
                currentIndex--;
            }
            updatePosition();
        }

        // Eventos de clique nos botões
        if (prevBtn) {
            prevBtn.addEventListener("click", (e) => {
                e.preventDefault();
                prevSlide();
                restartAutoplay();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener("click", (e) => {
                e.preventDefault();
                nextSlide();
                restartAutoplay();
            });
        }

        // Autoplay inteligente (pausa em hover ou touch)
        function startAutoplay() {
            stopAutoplay();
            autoplayTimer = setInterval(() => {
                nextSlide();
            }, 4500);
        }

        function stopAutoplay() {
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                autoplayTimer = null;
            }
        }

        function restartAutoplay() {
            stopAutoplay();
            startAutoplay();
        }

        container.addEventListener("mouseenter", stopAutoplay);
        container.addEventListener("mouseleave", startAutoplay);

        // Suporte a Touch e Swipe Mobile
        function touchStart(e) {
            isDragging = true;
            startX = e.type.includes("mouse") ? e.pageX : e.touches[0].clientX;
            stopAutoplay();
        }

        function touchMove(e) {
            if (!isDragging) return;
            const currentX = e.type.includes("mouse")
                ? e.pageX
                : e.touches[0].clientX;
            const diffX = currentX - startX;

            // Se o movimento for significativo, impede scroll vertical acidental no mobile
            if (
                Math.abs(diffX) > 10 &&
                e.cancelable &&
                !e.type.includes("mouse")
            ) {
                e.preventDefault();
            }
        }

        function touchEnd(e) {
            if (!isDragging) return;
            isDragging = false;
            const endX = e.type.includes("mouse")
                ? e.pageX
                : e.changedTouches
                  ? e.changedTouches[0].clientX
                  : startX;
            const diffX = endX - startX;

            // Limite de 40px para disparar a troca de slide
            if (diffX < -40) {
                nextSlide();
            } else if (diffX > 40) {
                prevSlide();
            }

            startAutoplay();
        }

        // Listeners Touch
        track.addEventListener("touchstart", touchStart, { passive: true });
        track.addEventListener("touchmove", touchMove, { passive: false });
        track.addEventListener("touchend", touchEnd, { passive: true });

        // Listeners Mouse Drag
        track.addEventListener("mousedown", touchStart);
        window.addEventListener("mouseup", (e) => {
            if (isDragging) touchEnd(e);
        });

        // Responsividade ao redimensionar
        window.addEventListener("resize", () => {
            renderDots();
            goToSlide(currentIndex);
        });

        // Inicialização
        renderDots();
        updatePosition();
        startAutoplay();
    });
}

// Auto-inicializa quando o DOM estiver pronto
if (typeof document !== "undefined") {
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", initCarrosel);
    } else {
        initCarrosel();
    }
}
