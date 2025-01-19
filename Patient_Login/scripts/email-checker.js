document.addEventListener("DOMContentLoaded", function () {
  const validation = new JustValidate("#a-form");
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  validation
    .addField("#firstName", [
      {
        rule: "required",
        errorMessage: "Enter your First Name",
      },
    ])
    .addField("#lastName", [
      {
        rule: "required",
        errorMessage: "Enter your Last Name",
      },
    ])
    
    .addField("#email", [
        {
          rule: "required",
          errorMessage: "Enter your email",
        },
        {
          rule: "email",
          errorMessage: "Email is invalid",
        },
        {
          validator: (value) => () => {
            return fetch("validate-email.php", {
              method: "POST", // Use POST to prevent sensitive data in URL
              headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken, // Include the CSRF token
              },
              body: JSON.stringify({ email: value }), // Send the email in the request body
            })
              .then((response) => response.json())
              .then((json) => json.available);
          },
          errorMessage: "Email invalid or already existed!",
        },
      ])
    .addField("#password", [
      {
        rule: "required",
        errorMessage: "Enter your password",
      },
      {
        rule: "password",
      },
      {
        rule: "minLength",
        value: 6,
        errorMessage: "Password must be at least 6 characters",
      },
    ])
    .addField("#rPassword", [
      {
        rule: "required",
        errorMessage: "Re-enter your password",
      },
      {
        validator: (value, fields) => {
          return value === fields["#password"].elem.value;
        },
        errorMessage: "Password should match",
      },
    ])
    .onSuccess((event) => {
      document.getElementById("a-form").submit();
    });
});
