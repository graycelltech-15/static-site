document.addEventListener("DOMContentLoaded", () => {
  const rules = {
    // letters, spaces, apostrophes, hyphens; at least 2 chars
    nameLike: v => /^[A-Za-z][A-Za-z\s'-]{1,}$/.test(v.trim()),
    companyLike: v => /^[A-Za-z0-9&.\-'\s]{2,}$/.test(v.trim()),
    numbersOnly: v => /^\d+$/.test(v.trim()),
    positiveInt: v => /^\d+$/.test(v.trim()) && parseInt(v, 10) > 0,
    minLen10: v => v.trim().length >= 10,
    minLen5: v => v.trim().length >= 5,
    email: v => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v.trim()),
    phone: v => {
      const digits = v.replace(/\D/g, "");
      return /^[0-9()+\-\s]{7,20}$/.test(v.trim()) && digits.length >= 7 && digits.length <= 15;
    }
  };

  const setError = (field, msg) => {
    const span = field.parentElement.querySelector(".error-msg");
    if (span) span.textContent = msg || "";
    field.classList.toggle("error-border", !!msg);
  };

    const validators = {
      // Application Enrollment
      name: (f) => rules.nameLike(f.value) ? null : "Only letters, spaces, ' and - (min 2 chars).",
      position: (f) => rules.nameLike(f.value) ? null : "Only letters, spaces, ' and -.",
      company: (f) => rules.companyLike(f.value) ? null : "Letters/numbers/& . - ' allowed.",
      "company-revenue": (f) => {
        if (f.form.id === "applicationForm" && f.value.trim() === "") return null; // optional
        return rules.numbersOnly(f.value) ? null : "Numbers only.";
      },
      "team-size": (f) => rules.positiveInt(f.value) ? null : "Enter a number greater than 0.",
      department: (f) => rules.nameLike(f.value) ? null : "Letters, spaces, ' and - only.",
      overview: () => null,
      "join-momentum": () => null,

      // Contact Us
      fname: (f) => rules.nameLike(f.value) ? null : "First name: letters only.",
      lname: (f) => rules.nameLike(f.value) ? null : "Last name: letters only.",
      email: (f) => rules.email(f.value) ? null : "Enter a valid email.",
      tel: (f) => {
        if (f.form.id === "applicationForm" && f.value.trim() === "") return null; // optional in application
        return rules.phone(f.value) ? null : "Phone: 7–15 digits (symbols allowed).";
      },
      msg: () => null
  };

    function validateForm(form) {
      let ok = true;
      const fields = form.querySelectorAll("input, textarea");
      fields.forEach(field => {
        const name = field.getAttribute("name");
        const vfn = validators[name];
        if (!vfn) return;

        let err = null;

        // If application form + optional field left blank → skip required msg
        if (
          form.id === "applicationForm" &&
          (name === "company-revenue" || name === "tel") &&
          field.value.trim() === ""
        ) {
          err = null;
        } else {
          err = field.value.trim() === "" ? "This field is required." : vfn(field);
        }

        setError(field, err);
        if (err) ok = false;
      });
      return ok;
    }

  function wireLiveValidation(form) {
    form.querySelectorAll("input, textarea").forEach(field => {
      const computeError = () => {
        const name = field.getAttribute("name");
        const vfn = validators[name];
        if (!vfn) return null;

        const isApplication = form.id === "applicationForm";
        const isOptionalInApplication = isApplication && (name === "company-revenue" || name === "tel");

        // Optional fields on application form: no error when empty
        if (isOptionalInApplication && field.value.trim() === "") return null;

        // Default behavior
        return field.value.trim() === "" ? "This field is required." : vfn(field);
      };

      field.addEventListener("input", () => {
        setError(field, computeError());
      });

      field.addEventListener("blur", () => {
        setError(field, computeError());
      });
    });
  }


  // Application form (AJAX + thank you)
  const applicationForm = document.getElementById("applicationForm");
  if (applicationForm) {
    wireLiveValidation(applicationForm);
    applicationForm.addEventListener("submit", (e) => {
      e.preventDefault();
      if (!validateForm(applicationForm)) return;

      fetch(applicationForm.getAttribute("action"), {
        method: "POST",
        body: new FormData(applicationForm)
      })
        .then(r => r.text())
        .then(t => {
          if (t.trim() === "success") {
        applicationForm.reset();
        applicationForm.querySelectorAll(".error-msg").forEach(s => (s.textContent = ""));
        applicationForm.querySelectorAll(".error-border").forEach(el => el.classList.remove("error-border"));
        
        const successEl = document.getElementById("application-success");
        successEl.style.display = "block";

        // Hide after 5 seconds
    setTimeout(() => {
        successEl.style.display = "none";
    }, 5000);
    }
 else {
            alert("There was a problem submitting the form. Please try again.");
          }
        })
        .catch(() => alert("Network error. Please try again."));
    });
  }

  // Contact form (AJAX; sends to admin + user)
  const contactForm = document.getElementById("contactForm");
  if (contactForm) {
    wireLiveValidation(contactForm);
    contactForm.addEventListener("submit", (e) => {
      e.preventDefault();
      if (!validateForm(contactForm)) return;

      fetch(contactForm.getAttribute("action"), {
        method: "POST",
        body: new FormData(contactForm)
      })
        .then(r => r.text())
        .then(t => {
          if (t.trim() === "success") {
            contactForm.reset();
            contactForm.querySelectorAll(".error-msg").forEach(s => (s.textContent = ""));
            contactForm.querySelectorAll(".error-border").forEach(el => el.classList.remove("error-border"));
           
             const successEl = document.getElementById("contact-success");
        successEl.style.display = "block";

             setTimeout(() => {
        successEl.style.display = "none";
    }, 5000);
          } else {
            alert("There was a problem submitting the form. Please try again.");
          }
        })
        .catch(() => alert("Network error. Please try again."));
    });
  }
});
