// fade-in hero text
window.addEventListener("load", () => {
    const text = document.querySelector(".hero-text");
    text.style.opacity = 0;
    text.style.transform = "translate(-50%, -40%)";

    setTimeout(() => {
        text.style.transition = "0.8s";
        text.style.opacity = 1;
        text.style.transform = "translate(-50%, -50%)";
    }, 200);
});