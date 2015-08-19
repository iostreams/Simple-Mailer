/*
 * Copyright (C) 2015 ioerror
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$(document).ready(function () {
    function clearAlerts() {
        $("#alerts").empty().addClass("hidden");
    }

    function addAlerts(messages, type) {
        var div;
        var n = messages.length;
        $("#alerts").removeClass();
        for (var i = 0; i < n; i++) {
            div = $("<div>", {role: "alert", class: "alert alert-"+type});
            div.text(messages[i]);
            $("#alerts").append(div);
        }
    }

    function clearFieldsError() {
        $("form#contactForm .has-error.has-feedback span.glyphicon.glyphicon-remove.form-control-feedback").remove();
        $("form#contactForm .has-error.has-feedback").removeClass("has-error has-feedback");
    }

    function setFieldsError(fields) {
        var spanIcon, element;
        var n = fields.length;
        for (var i = 0; i < n; i++) {
            spanIcon = $("<span>", {class: "glyphicon glyphicon-remove form-control-feedback"});
            element = $("#"+fields[i]);
            element.parent().addClass("has-error has-feedback");
            element.after(spanIcon);
        }
    }

    $("form#contactForm").submit(function (event) {
        $.ajax({
            type: "POST",
            url: "response.php",
            dataType: "json",
            data: $(this).serializeArray(),
            success: function (response) {
                clearAlerts();
                clearFieldsError();
                if (!response.status || (response.status != "error" && response.status != "ok")) {
                    addAlerts(["Błąd odpowiedzi serwera"], 'danger');
                }
                else if (response.status == "ok") {
                    addAlerts(response.messages, 'success');
                    $("form#contactForm #subject").val("");
                    $("form#contactForm #message").val("");
                }
                else if (response.status == "error") {
                    addAlerts(response.messages, 'danger');
                    setFieldsError(response.errorsIn);
                }
            },
            error: function (error) {
                clearAlerts();
                clearFieldsError();
                addAlerts(["Błąd przesyłania danych do serwera"], 'danger');
            }
        });

        event.preventDefault();
    });

    //clear no JS alert
    clearAlerts();

});



