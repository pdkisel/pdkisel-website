document.addEventListener("DOMContentLoaded", function() {
    // 1. LOGIKA MENU MOBILE
    const mobileMenuBtn = document.getElementById('mobile-menu');
    const navLinks = document.querySelector('.nav-links');
    const icon = mobileMenuBtn ? mobileMenuBtn.querySelector('i') : null;

    if (mobileMenuBtn && navLinks && icon) {
        mobileMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            navLinks.classList.toggle('active');
            if (navLinks.classList.contains('active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
    }

    // 2. EFEK NAVBAR SAAT SCROLL
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.style.padding = '10px 0';
                navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.2)';
            } else {
                navbar.style.padding = '15px 0';
                navbar.style.boxShadow = 'none';
            }
        });
    }

    // 3. ANIMASI ANGKA STATISTIK (TANPA TANDA +)
    const counters = document.querySelectorAll('.counter');
    const speed = 200; 
    
    const animateCounter = (counter) => {
        const target = +counter.getAttribute('data-target');
        const count = +counter.innerText;
        const inc = target / speed;

        if (count < target) {
            counter.innerText = Math.ceil(count + inc);
            setTimeout(() => animateCounter(counter), 20);
        } else {
            // Ini yang kita ubah: Hanya menampilkan angka target, tanpa "+"
            counter.innerText = target; 
        }
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => {
        observer.observe(counter);
    });
});
