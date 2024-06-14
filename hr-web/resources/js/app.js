// Wait for the DOM content to be fully loaded
document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded and parsed");

    // Test GSAP animation
    gsap.from(".gradient-text", {
        opacity: 0,
        y: 50,
        duration: 1.5,
        ease: "power3.out"
    });
});
