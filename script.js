document.addEventListener("DOMContentLoaded", () => {
  // Smooth scrolling for navigation links
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      document.querySelector(this.getAttribute("href")).scrollIntoView({
        behavior: "smooth",
      });
    });
  });

  // Contact form handling
  const contactForm = document.getElementById("contact-form");
  if (contactForm) {
    contactForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const formData = {
        name: document.getElementById("name").value,
        email: document.getElementById("email").value,
        message: document.getElementById("message").value,
      };

      try {
        const response = await fetch("save_message.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(formData),
        });

        const result = await response.json();

        // Show notification
        showNotification(result.message, result.success ? "success" : "error");

        if (result.success) {
          contactForm.reset();
        }
      } catch (error) {
        showNotification("خطا در ارسال پیام. لطفاً دوباره تلاش کنید.", "error");
      }
    });
  }

  // Notification system
  function showNotification(message, type = "success") {
    const notification = document.createElement("div");
    notification.className = `notification ${type}`;
    notification.textContent = message;

    document.body.appendChild(notification);

    // Add styles
    notification.style.position = "fixed";
    notification.style.bottom = "20px";
    notification.style.left = "20px";
    notification.style.padding = "1rem 2rem";
    notification.style.borderRadius = "8px";
    notification.style.color = "white";
    notification.style.fontFamily = "Vazirmatn, sans-serif";
    notification.style.zIndex = "1000";
    notification.style.animation = "slideIn 0.3s ease-out";

    if (type === "success") {
      notification.style.backgroundColor = "#4CAF50";
    } else {
      notification.style.backgroundColor = "#f44336";
    }

    // Remove notification after 3 seconds
    setTimeout(() => {
      notification.style.animation = "slideOut 0.3s ease-out";
      setTimeout(() => {
        document.body.removeChild(notification);
      }, 300);
    }, 3000);
  }

  // Add animation styles
  const style = document.createElement("style");
  style.textContent = `
    @keyframes slideIn {
      from {
        transform: translateX(-100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    @keyframes slideOut {
      from {
        transform: translateX(0);
        opacity: 1;
      }
      to {
        transform: translateX(-100%);
        opacity: 0;
      }
    }
  `;
  // Animate skill bars on scroll
  const skillBars = document.querySelectorAll(".progress");
  const observerOptions = {
    threshold: 0.5,
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.style.width = entry.target.style.width;
      }
    });
  }, observerOptions);

  skillBars.forEach((bar) => observer.observe(bar));

  // Add mobile menu functionality
  const createMobileMenu = () => {
    const nav = document.querySelector("nav");
    const mobileMenuButton = document.createElement("button");
    mobileMenuButton.classList.add("mobile-menu-button");
    mobileMenuButton.innerHTML = '<i class="fas fa-bars"></i>';

    const mobileMenu = document.createElement("div");
    mobileMenu.classList.add("mobile-menu");
    mobileMenu.innerHTML = nav.querySelector(".nav-links").innerHTML;

    nav.appendChild(mobileMenuButton);
    document.body.appendChild(mobileMenu);

    mobileMenuButton.addEventListener("click", () => {
      mobileMenu.classList.toggle("active");
      mobileMenuButton.classList.toggle("active");
    });
  };

  // Create mobile menu if screen width is small
  if (window.innerWidth <= 768) {
    createMobileMenu();
  }

  // Handle window resize
  window.addEventListener("resize", () => {
    if (
      window.innerWidth <= 768 &&
      !document.querySelector(".mobile-menu-button")
    ) {
      createMobileMenu();
    }
  });
});
