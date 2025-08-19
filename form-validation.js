// document.addEventListener("DOMContentLoaded", () => {

//     function validateInput(input, regex, message) {
//         const errorSpan = input.nextElementSibling;
//         if (!regex.test(input.value.trim())) {
//             input.classList.add("error");
//             errorSpan.textContent = message;
//             return false;
//         }
//         input.classList.remove("error");
//         errorSpan.textContent = "";
//         return true;
//     }

//     function showSuccessMessage(form, text) {
//         const successDiv = form.querySelector(".success-message");
//         successDiv.textContent = text;
//         successDiv.style.display = "block";
//         setTimeout(() => {
//             successDiv.style.display = "none";
//             form.reset();
//         }, 5000);
//     }

//     function attachValidation(form, type) {
//         form.addEventListener("submit", function (e) {
//             e.preventDefault();

//             let isValid = true;

//             const nameFields = form.querySelectorAll("input[name='name'], input[name='fname'], input[name='lname']");
//             nameFields.forEach(input => {
//                 if (!validateInput(input, /^[A-Za-z\s]+$/, "Only letters are allowed")) isValid = false;
//             });

//             const emailField = form.querySelector("input[name='email']");
//             if (emailField && !validateInput(emailField, /^[^@\s]+@[^@\s]+\.[^@\s]+$/, "Invalid email")) {
//                 isValid = false;
//             }

//             const phoneField = form.querySelector("input[name='tel']");
//             if (phoneField && !validateInput(phoneField, /^[0-9]{7,15}$/, "Invalid phone number")) {
//                 isValid = false;
//             }

//             const numberField = form.querySelector("input[name='team-size']");
//             if (numberField && !validateInput(numberField, /^[0-9]+$/, "Enter a valid number")) {
//                 isValid = false;
//             }

//             form.querySelectorAll("textarea").forEach(textarea => {
//                 if (!validateInput(textarea, /.+/, "This field is required")) isValid = false;
//             });

//             if (!isValid) return;

//             const formData = new FormData(form);
//             formData.append("formType", type);

//             fetch("send-mail.php", {
//                 method: "POST",
//                 body: formData
//             })
//                 .then(res => res.text())
//                 .then(response => {
//                     showSuccessMessage(form, type === "contact" ?
//                         "Thank you! Your message has been sent." :
//                         "Thank you! Your application has been submitted."
//                     );
//                 })
//                 .catch(err => console.error("Error:", err));
//         });
//     }

//     document.querySelectorAll(".contact-form").forEach(f => attachValidation(f, "contact"));
//     document.querySelectorAll(".application-form").forEach(f => attachValidation(f, "application"));
// });
