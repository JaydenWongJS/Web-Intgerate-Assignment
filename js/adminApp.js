document.addEventListener('DOMContentLoaded', (event) => {
  const manageDropdown = document.getElementById("management");
  const dropdownMenu = document.getElementById("dropdownMenu");

  manageDropdown.addEventListener("click", function(event) {
      event.preventDefault(); // Prevent default action of the link
      if (dropdownMenu.classList.contains("show")) {
          dropdownMenu.classList.remove("show");
          setTimeout(() => {
              dropdownMenu.style.display = "none";
          }, 500); // Delay to allow transition to complete before setting display to none
      } else {
          dropdownMenu.style.display = "block";
          setTimeout(() => {
              dropdownMenu.classList.add("show");
          }, 10); // Small delay to ensure display is set to block before adding class
      }
  });

  // Show dropdown if current page is within the management section
  const managementPages = ['Admin Order', 'Members', 'Reviews'];
  if (managementPages.includes(currentPageTitle)) {
      dropdownMenu.style.display = "block";
      setTimeout(() => {
          dropdownMenu.classList.add("show");
      }, 10); // Small delay to ensure display is set to block before adding class
  }
});


document.addEventListener('DOMContentLoaded', () => {
  const circle = document.querySelector('.circle');
  const positivePercentage = circle.getAttribute('data-positive');
  const neutralPercentage = circle.getAttribute('data-neutral');
  const negativePercentage = circle.getAttribute('data-negative');

  circle.style.background = `conic-gradient(
      #4caf50 0% ${positivePercentage}%, 
      #ddd ${positivePercentage}% ${parseFloat(positivePercentage) + parseFloat(neutralPercentage)}%, 
      #f44336 ${parseFloat(positivePercentage) + parseFloat(neutralPercentage)}% 100%
  )`;
});

    function submitSortingSize() {
        document.getElementById('sortingForm').submit();
    }

