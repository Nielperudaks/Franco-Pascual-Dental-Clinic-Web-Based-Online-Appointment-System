document.addEventListener("DOMContentLoaded", function () {
  const validation = new JustValidate("#a-form");

  validation
   
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
