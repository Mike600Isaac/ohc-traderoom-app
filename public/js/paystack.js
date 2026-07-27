function getSubscribeUrl(courseId) {
  return `/subscribe/${encodeURIComponent(courseId)}`;
}

document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".paystack-btn").forEach((btn) => {
    btn.addEventListener("click", function (event) {
      event.preventDefault();
      event.stopPropagation();

      const courseId = this.dataset.courseId;

      if (!courseId) {
        alert("Invalid course selected");
        return;
      }

      window.location.href = getSubscribeUrl(courseId);
    });
  });
});