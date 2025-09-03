/**
 * JavaScript del admin del plugin Event Calendar Plugin
 */

(function ($) {
  "use strict";

  // Inicializar cuando el documento esté listo
  $(document).ready(function () {
    ECPAdmin.init();
  });

  // Objeto del admin del plugin
  var ECPAdmin = {
    // Inicializar
    init: function () {
      this.bindEvents();
      this.initDatePicker();
      this.initImageUpload();
      this.initFormValidation();
    },

    // Vincular eventos
    bindEvents: function () {
      // Subir flyer
      $(document).on("change", "#flyer", this.handleFlyerUpload);

      // Validar formulario
      $(document).on("submit", 'form[method="post"]', this.validateForm);

      // Confirmar eliminación
      $(document).on("click", 'a[href*="action=delete"]', this.confirmDelete);

      // Exportar attendees
      $(document).on("click", 'a[href*="action=export"]', this.handleExport);

      // Auto-guardar borrador
      $(document).on("input", "input, textarea, select", this.autoSaveDraft);
    },

    // Inicializar datepicker
    initDatePicker: function () {
      if ($.fn.datepicker) {
        $("#event_date").datetimepicker({
          dateFormat: "yy-mm-dd",
          timeFormat: "HH:mm",
          showButtonPanel: true,
          changeMonth: true,
          changeYear: true,
          yearRange: "c-1:c+2",
        });
      }
    },

    // Inicializar subida de imágenes
    initImageUpload: function () {
      // Si estamos en la página de edición de eventos
      if ($("#flyer").length) {
        this.initMediaUploader();
      }
    },

    // Inicializar media uploader
    initMediaUploader: function () {
      const uploader = wp.media({
        title: "Seleccionar Flyer",
        button: {
          text: "Usar esta imagen",
        },
        multiple: false,
      });

      $("#flyer").on("click", function (e) {
        e.preventDefault();
        uploader.open();
      });

      uploader.on("select", function () {
        const attachment = uploader.state().get("selection").first().toJSON();
        $("#flyer").val(attachment.url);
        $(".current-flyer").html(
          '<img src="' +
            attachment.url +
            '" style="max-width: 200px; height: auto;"><p><a href="' +
            attachment.url +
            '" target="_blank">Ver flyer actual</a></p>'
        );
      });
    },

    // Inicializar validación de formulario
    initFormValidation: function () {
      // Validación en tiempo real
      $(document).on(
        "blur",
        "input[required], textarea[required]",
        function () {
          ECPAdmin.validateField($(this));
        }
      );
    },

    // Manejar subida de flyer
    handleFlyerUpload: function () {
      const file = this.files[0];
      if (file) {
        // Validar tipo de archivo
        if (!file.type.match("image.*")) {
          alert("Por favor selecciona una imagen válida.");
          $(this).val("");
          return;
        }

        // Validar tamaño (máximo 5MB)
        if (file.size > 5 * 1024 * 1024) {
          alert("La imagen es demasiado grande. Máximo 5MB.");
          $(this).val("");
          return;
        }

        // Mostrar preview
        const reader = new FileReader();
        reader.onload = function (e) {
          $(".current-flyer").html(
            '<img src="' +
              e.target.result +
              '" style="max-width: 200px; height: auto;">'
          );
        };
        reader.readAsDataURL(file);
      }
    },

    // Validar formulario
    validateForm: function (e) {
      let isValid = true;
      const form = $(this);

      // Validar campos requeridos
      form.find("input[required], textarea[required]").each(function () {
        if (!ECPAdmin.validateField($(this))) {
          isValid = false;
        }
      });

      // Validar fecha
      const eventDate = form.find("#event_date").val();
      if (eventDate) {
        const selectedDate = new Date(eventDate);
        const now = new Date();

        if (selectedDate < now) {
          alert("La fecha del evento debe ser futura.");
          isValid = false;
        }
      }

      if (!isValid) {
        e.preventDefault();
        return false;
      }
    },

    // Validar campo individual
    validateField: function (field) {
      const value = field.val().trim();
      const fieldName = field.attr("name");
      let isValid = true;
      let errorMessage = "";

      // Validar campos requeridos
      if (field.prop("required") && !value) {
        isValid = false;
        errorMessage = "Este campo es obligatorio";
      }

      // Validar email si es campo de email
      if (fieldName === "email" && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
          isValid = false;
          errorMessage = "Email no válido";
        }
      }

      // Validar URL si es campo de URL
      if (fieldName === "registration_link" && value) {
        try {
          new URL(value);
        } catch (e) {
          isValid = false;
          errorMessage = "URL no válida";
        }
      }

      // Mostrar/ocultar error
      if (isValid) {
        field.removeClass("error");
        field.next(".field-error").remove();
      } else {
        field.addClass("error");
        if (!field.next(".field-error").length) {
          field.after(
            '<div class="field-error" style="color: #dc3545; font-size: 0.8rem; margin-top: 5px;">' +
              errorMessage +
              "</div>"
          );
        }
      }

      return isValid;
    },

    // Confirmar eliminación
    confirmDelete: function (e) {
      if (
        !confirm(
          "¿Estás seguro de que quieres eliminar este evento? Esta acción no se puede deshacer."
        )
      ) {
        e.preventDefault();
        return false;
      }
    },

    // Manejar exportación
    handleExport: function (e) {
      e.preventDefault();

      const url = $(this).attr("href");
      const eventId = url.match(/event_id=(\d+)/)[1];

      // Mostrar mensaje de carga
      const originalText = $(this).text();
      $(this).text("Exportando...").prop("disabled", true);

      // Crear enlace de descarga
      const downloadLink = document.createElement("a");
      downloadLink.href = url;
      downloadLink.download = "attendees_evento_" + eventId + ".csv";
      document.body.appendChild(downloadLink);
      downloadLink.click();
      document.body.removeChild(downloadLink);

      // Restaurar botón
      setTimeout(
        function () {
          $(this).text(originalText).prop("disabled", false);
        }.bind(this),
        1000
      );
    },

    // Auto-guardar borrador
    autoSaveDraft: function () {
      const form = $(this).closest("form");
      const formData = form.serialize();

      // Debounce para evitar muchas llamadas
      clearTimeout(this.autoSaveTimeout);
      this.autoSaveTimeout = setTimeout(function () {
        $.ajax({
          url: ajaxurl,
          type: "POST",
          data: {
            action: "ecp_auto_save_draft",
            form_data: formData,
            nonce: $("#ecp_nonce").val(),
          },
          success: function (response) {
            if (response.success) {
              console.log("Borrador guardado automáticamente");
            }
          },
        });
      }, 2000);
    },

    // Utilidades
    utils: {
      // Formatear fecha
      formatDate: function (date) {
        const options = {
          year: "numeric",
          month: "long",
          day: "numeric",
          hour: "2-digit",
          minute: "2-digit",
        };
        return new Date(date).toLocaleDateString("es-ES", options);
      },

      // Mostrar notificación
      showNotification: function (message, type) {
        const notification = $(
          '<div class="ecp-notification ecp-notification-' +
            type +
            '">' +
            message +
            "</div>"
        );
        $("body").append(notification);

        setTimeout(function () {
          notification.fadeOut(function () {
            notification.remove();
          });
        }, 3000);
      },

      // Debounce
      debounce: function (func, wait, immediate) {
        let timeout;
        return function () {
          const context = this;
          const args = arguments;
          const later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
          };
          const callNow = immediate && !timeout;
          clearTimeout(timeout);
          timeout = setTimeout(later, wait);
          if (callNow) func.apply(context, args);
        };
      },
    },
  };

  // Exponer ECPAdmin globalmente
  window.ECPAdmin = ECPAdmin;
})(jQuery);
