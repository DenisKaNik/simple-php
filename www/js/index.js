$(function() {
    $(".btn").click(function() {
        $(".form-signin").toggleClass("form-signin-left");
        $(".form-signup").toggleClass("form-signup-left");
        $(".frame").toggleClass("frame-long");
        $(".signup-inactive").toggleClass("signup-active");
        $(".signin-active").toggleClass("signin-inactive");
        $(".forgot").toggleClass("forgot-left");
        $(this).removeClass("idle").addClass("active");
    });
});

let project = { modules: [] };

project.extend = function(moduleName, moduleData) {
    if (!moduleName) {
        return;
    }
    if (!moduleData) {
        let moduleData = {
            elements: {},
            init: () => {
                //console.log("Empty init for module");
            }
        };
    }
    this[moduleName] = moduleData;
    this.modules.push(moduleData);
    return moduleData;
};

project.init = function() {
    let totalModules = project.modules.length;
    for (let k = 0; k < totalModules; k++) {
        project.modules[k].init();
    }
};

project.extend("common", {
    init: function() {
        let self = this,
            $registrationForm = $("form.form-signup").not('.form-changepwd'),
            $authorizationForm = $("form.form-signin").not('.form-personal'),
            $personalForm = $("form.form-personal"),
            $changePwdForm = $("form.form-changepwd"),
            $logoutForm = $("form.form-logout");

        $registrationForm.find(".btn-signup").on("click", function() {
            return self.sendRegistration($registrationForm);
        });

        $authorizationForm.find(".btn-animate").on("click", function() {
            return self.sendAuthorization($authorizationForm);
        });

        $personalForm.find(".btn-animate").on("click", function() {
            return self.sendPersonalUpdate($personalForm);
        });

        $changePwdForm.find(".btn-signup").on("click", function() {
            return self.sendChangePwd($changePwdForm);
        });

        $logoutForm.find(".btn-goback").on("click", function() {
            return self.sendLogout($logoutForm);
        });
    },

    sendRegistration: function(form) {
        let f = form[0];

        $(f)
            .find("input").removeClass('error')
            .end()
            .find("div.error").remove();

        $.ajax({
            url: "/api/registr",
            type: "POST",
            data: new FormData(f),
            contentType: false,
            dataType: "json",
            processData: false,
            cache: false,
            success: function(data) {
                $(".nav").toggleClass("nav-up");
                $(".form-signup-left").toggleClass("form-signup-down");
                $(".success").toggleClass("success-left");
                $(".frame").toggleClass("frame-short");
            },
            error: function(data) {
                if (data.responseJSON) {
                    if (data.responseJSON.message !== undefined) {
                        $(f).append('<div class="error">' + data.responseJSON.message + '</div>');
                    } else if (data.responseJSON.errors !== undefined) {
                        $.each(data.responseJSON.errors, function(k, val) {
                            $(f).find('input[name="'+k+'"]')
                                .addClass('error')
                                .attr('placeholder', val);
                        });
                    }
                }
            }
        });

        return false;
    },

    sendAuthorization: function(form) {
        let f = form[0];

        $(f)
            .find("input").removeClass('error')
            .end()
            .find("div.error").remove();

        $.ajax({
            url: "/api/login",
            type: "POST",
            data: new FormData(f),
            contentType: false,
            dataType: "json",
            processData: false,
            cache: false,
            success: function(data) {
                if (data.success && data.username) {
                    $('.welcome').find('span').text(data.username);

                    $(".btn-animate").toggleClass("btn-animate-grow");
                    $(".welcome").toggleClass("welcome-left");
                    $(".cover-photo").toggleClass("cover-photo-down");
                    $(".frame").toggleClass("frame-short");
                    $(".profile-photo").toggleClass("profile-photo-down");
                    $(".btn-goback").toggleClass("btn-goback-up");
                    $(".forgot").toggleClass("forgot-fade");
                }
            },
            error: function(data) {
                if (data.responseJSON) {
                    if (data.responseJSON.message !== undefined) {
                        $(f).append('<div class="error">' + data.responseJSON.message + '</div>');
                    } else if (data.responseJSON.errors !== undefined) {
                        $.each(data.responseJSON.errors, function(k, val) {
                            $(f).find('input[name="'+k+'"]')
                                .addClass('error')
                                .attr('placeholder', val);
                        });
                    }
                }
            }
        });

        return false;
    },

    sendPersonalUpdate: function(form) {
        let f = form[0];

        $(f)
            .find("input").removeClass('error')
            .end()
            .find("div.error, div.success-msg").remove();

        $.ajax({
            url: "/api/personal",
            type: "POST",
            data: new FormData(f),
            contentType: false,
            dataType: "json",
            processData: false,
            cache: false,
            success: function(data) {
                if (data.success && data.message) {
                    $(f).append('<div class="success-msg">' + data.message + '</div>');
                }
            },
            error: function(data) {
                if (data.responseJSON) {
                    if (data.responseJSON.message !== undefined) {
                        $(f).append('<div class="error">' + data.responseJSON.message + '</div>');
                    } else if (data.responseJSON.errors !== undefined) {
                        $.each(data.responseJSON.errors, function(k, val) {
                            $(f).find('input[name="'+k+'"]')
                                .addClass('error')
                                .attr('placeholder', val);
                        });
                    }
                }
            }
        });

        return false;
    },

    sendChangePwd: function(form) {
        let f = form[0];

            $(f)
                .find("input").removeClass('error')
                .end()
                .find("div.error, div.success-msg").remove();

        $.ajax({
            url: "/api/change-pwd",
            type: "POST",
            data: new FormData(f),
            contentType: false,
            dataType: "json",
            processData: false,
            cache: false,
            success: function(data) {
                if (data.success && data.message) {
                    $(f).append('<div class="success-msg">' + data.message + '</div>');
                }
            },
            error: function(data) {
                if (data.responseJSON) {
                    if (data.responseJSON.message !== undefined) {
                        //console.log(data.responseJSON.message);
                    } else if (data.responseJSON.errors !== undefined) {
                        $.each(data.responseJSON.errors, function(k, val) {
                            $(f).find('input[name="'+k+'"]').addClass('error');
                            $(f).append('<div class="error">' + val + '</div>');
                        });
                    }
                }
            }
        });

        return false;
    },

    sendLogout: function(form) {
        let f = form[0];

        $(f).find("input").removeClass('error');

        $.ajax({
            url: "/api/logout",
            type: "POST",
            data: new FormData(f),
            contentType: false,
            dataType: "json",
            processData: false,
            cache: false,
            success: function(data) {
                if (data.success) {
                    document.location.href = '/';
                }
            }
        });

        return false;
    }
});

$(project.init);