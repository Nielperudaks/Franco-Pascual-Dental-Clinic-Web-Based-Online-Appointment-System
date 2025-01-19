document.addEventListener('DOMContentLoaded', function () {
    const validation = new JustValidate("#a-form");
    // .addField("#firstName", [
    //     {
    //         rule: "required",
    //         errorMessage: "Enter your First Name"
    //     }
        
        
    // ]).addField("#lastName", [
    //     {
    //         rule: "required",
    //         errorMessage: "Enter your Last Name"
    //     }
        
    // ]).addField("#middleName", [
    //     {
    //         rule: "required",
    //         errorMessage: "Enter your Middle Name"
    //     }
        
    // ]).addField("#number", [
    //     {
    //         rule: "required",
    //         errorMessage: "Enter your Phone Number"
    //     }
        
    // ])

    validation
        .addField("#email", [
            {
                rule: "required",
                errorMessage: "Enter your email"
            },
            {
                rule: "email",
                errorMessage: 'Email is invalid',
            },
            {
                validator: (value) => () => {
                    return fetch("validate-email.php?email=" + encodeURIComponent(value))
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (json) {
                            return json.available;
                        });
                },
                errorMessage: "Email already existed!"
            }
        ])
        .addField("#password", [
            {
                rule: "required",
                errorMessage: "Enter your password"
            },
            {
                rule: "password"
            },
            {
                rule: 'minLength',
                value: 6,
                errorMessage: 'Password must be at least 6 characters',
            },
        ])
        .addField("#rPassword", [
            {
                rule: "required",
                errorMessage: "Re-enter your password"
            },
            {
                validator: (value, fields) => {
                    return value === fields["#password"].elem.value;
                },
                errorMessage: "Password should match"
            }
        ])
        .onSuccess((event) => {
            document.getElementById("a-form").submit();
        });
});
