/**
 * JavaScript público del plugin Event Calendar Plugin
 */

(function ($) {
  "use strict";

  // Inicializar cuando el documento esté listo
  $(document).ready(function () {
    ECPPublic.init();
  });

  // Objeto público del plugin
  var ECPPublic = {
    // Inicializar
    init: function () {
      this.bindEvents();
      this.initModals();
      this.initRegistrationForm();
      this.initEventCards();
      this.initFilters();
    },

    // Vincular eventos
    bindEvents: function () {
      // Mostrar detalles del evento
      $(document).on("click", ".ecp-show-details", this.showEventDetails);

      // Mostrar formulario de registro
      $(document).on(
        "click",
        ".ecp-show-registration",
        this.showRegistrationForm
      );

      // Cerrar modales
      $(document).on("click", ".ecp-modal-close", this.closeModal);

      // Cerrar modal al hacer clic fuera
      $(document).on("click", ".ecp-modal", function (e) {
        if (e.target === this) {
          ECPPublic.closeModal();
        }
      });

      // Enviar formulario de registro
      $(document).on("submit", "#ecp-register-form", this.handleRegistration);

      // Filtros de eventos
      $(document).on("change", ".ecp-event-filter", this.filterEvents);

      // Búsqueda de eventos
      $(document).on("input", ".ecp-event-search", this.searchEvents);
    },

    // Inicializar modales
    initModals: function () {
      // Agregar estilos para el body cuando hay modal abierto
      $(document).on(
        "click",
        ".ecp-show-details, .ecp-show-registration",
        function () {
          $("body").addClass("ecp-modal-open");
        }
      );

      $(document).on("click", ".ecp-modal-close", function () {
        $("body").removeClass("ecp-modal-open");
      });
    },

    // Inicializar formulario de registro
    initRegistrationForm: function () {
      // Validación en tiempo real
      $(document).on("blur", "#ecp-register-form input[required]", function () {
        ECPPublic.validateField($(this));
      });
    },

    // Inicializar tarjetas de eventos
    initEventCards: function () {
      // Animación de entrada
      $(".ecp-event-card").each(function (index) {
        $(this).css("animation-delay", index * 0.1 + "s");
      });

      // Lazy loading para imágenes
      if ("IntersectionObserver" in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              const img = entry.target;
              img.src = img.dataset.src;
              img.classList.remove("lazy");
              imageObserver.unobserve(img);
            }
          });
        });

        document.querySelectorAll("img[data-src]").forEach((img) => {
          imageObserver.observe(img);
        });
      }
    },

    // Inicializar filtros
    initFilters: function () {
      // Crear interfaz de filtros si no existe
      if ($(".ecp-event-filters").length === 0) {
        this.createFiltersInterface();
      }
    },

    // Crear interfaz de filtros
    createFiltersInterface: function () {
      const filtersHtml = `
                <div class="ecp-event-filters">
                    <div class="ecp-filter-group">
                        <label for="ecp-date-filter">Filtrar por fecha:</label>
                        <select id="ecp-date-filter" class="ecp-event-filter">
                            <option value="all">Todos los eventos</option>
                            <option value="upcoming">Próximos eventos</option>
                            <option value="past">Eventos pasados</option>
                            <option value="this-month">Este mes</option>
                        </select>
                    </div>
                    
                    <div class="ecp-filter-group">
                        <label for="ecp-search">Buscar eventos:</label>
                        <input type="text" id="ecp-search" class="ecp-event-search" placeholder="Buscar por título o descripción...">
                    </div>
                </div>
            `;

      $(".ecp-events-container").prepend(filtersHtml);
    },

    // Mostrar detalles del evento
    showEventDetails: function (e) {
      e.preventDefault();

      const eventId = $(this).data("event-id");
      const modal = $("#ecp-event-modal-" + eventId);

      if (modal.length) {
        modal.show();
        $("body").addClass("ecp-modal-open");
      }
    },

    // Mostrar formulario de registro
    showRegistrationForm: function (e) {
      e.preventDefault();

      const eventId = $(this).data("event-id");
      const modal = $("#ecp-registration-modal");
      const container = $("#ecp-registration-form-container");

      // Cargar formulario de registro
      container.html('<div class="ecp-loading"></div>');

      // Simular carga del formulario (en una implementación real, esto sería una llamada AJAX)
      setTimeout(function () {
        const formHtml = ECPPublic.generateRegistrationForm(eventId);
        container.html(formHtml);
        modal.show();
        $("body").addClass("ecp-modal-open");
      }, 500);
    },

    // Generar formulario de registro
    generateRegistrationForm: function (eventId) {
      return `
                <form id="ecp-register-form" data-event-id="${eventId}">
                    <div class="ecp-form-row">
                        <div class="ecp-form-group">
                            <label for="first_name">Nombre *</label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        <div class="ecp-form-group">
                            <label for="last_name">Apellido *</label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    
                    <div class="ecp-form-row">
                        <div class="ecp-form-group">
                            <label for="position">Posición</label>
                            <input type="text" id="position" name="position">
                        </div>
                        <div class="ecp-form-group">
                            <label for="company">Empresa</label>
                            <input type="text" id="company" name="company">
                        </div>
                    </div>
                    
                    <div class="ecp-form-row">
                        <div class="ecp-form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="ecp-form-group">
                            <label for="phone">Teléfono</label>
                            <input type="tel" id="phone" name="phone">
                        </div>
                    </div>
                    
                    <div class="ecp-form-actions">
                        <button type="submit" class="ecp-register-btn">Registrarse</button>
                    </div>
                    
                    <div class="ecp-form-message" style="display: none;"></div>
                </form>
            `;
    },

    // Cerrar modal
    closeModal: function () {
      $(".ecp-modal").hide();
      $("body").removeClass("ecp-modal-open");
    },

    // Manejar registro
    handleRegistration: function (e) {
      e.preventDefault();

      const form = $(this);
      const formData = form.serialize();
      const submitBtn = form.find('button[type="submit"]');
      const messageDiv = form.find(".ecp-form-message");

      // Validar formulario
      if (!ECPPublic.validateForm(form)) {
        return;
      }

      // Mostrar estado de carga
      submitBtn.prop("disabled", true).text("Registrando...");
      form.addClass("ecp-loading");

      // Enviar datos
      $.ajax({
        url: ecp_ajax.ajax_url,
        type: "POST",
        data: {
          action: "ecp_register_attendee",
          nonce: ecp_ajax.nonce,
          event_id: form.data("event-id"),
          first_name: form.find("#first_name").val(),
          last_name: form.find("#last_name").val(),
          position: form.find("#position").val(),
          company: form.find("#company").val(),
          email: form.find("#email").val(),
          phone: form.find("#phone").val(),
        },
        success: function (response) {
          if (response.success) {
            messageDiv
              .removeClass("error")
              .addClass("success")
              .text(response.data)
              .show();
            form[0].reset();

            // Cerrar modal después de 3 segundos
            setTimeout(function () {
              ECPPublic.closeModal();
            }, 3000);
          } else {
            messageDiv
              .removeClass("success")
              .addClass("error")
              .text(response.data)
              .show();
          }
        },
        error: function () {
          messageDiv
            .removeClass("success")
            .addClass("error")
            .text("Error al procesar el registro. Inténtalo de nuevo.")
            .show();
        },
        complete: function () {
          submitBtn.prop("disabled", false).text("Registrarse");
          form.removeClass("ecp-loading");
        },
      });
    },

    // Validar formulario
    validateForm: function (form) {
      let isValid = true;

      form.find("input[required]").each(function () {
        if (!ECPPublic.validateField($(this))) {
          isValid = false;
        }
      });

      return isValid;
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

      // Validar email
      if (fieldName === "email" && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
          isValid = false;
          errorMessage = "Email no válido";
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

    // Filtrar eventos
    filterEvents: function () {
      const filter = $(this).val();
      const eventsContainer = $(".ecp-events-container");
      const events = eventsContainer.find(".ecp-event-card");

      events.each(function () {
        const event = $(this);
        const eventDate = new Date(event.data("event-date"));
        const now = new Date();

        let show = true;

        switch (filter) {
          case "upcoming":
            show = eventDate > now;
            break;
          case "past":
            show = eventDate < now;
            break;
          case "this-month":
            const thisMonth = now.getMonth();
            const thisYear = now.getFullYear();
            show =
              eventDate.getMonth() === thisMonth &&
              eventDate.getFullYear() === thisYear;
            break;
        }

        if (show) {
          event.fadeIn();
        } else {
          event.fadeOut();
        }
      });
    },

    // Buscar eventos
    searchEvents: function () {
      const searchTerm = $(this).val().toLowerCase();
      const events = $(".ecp-event-card");

      events.each(function () {
        const event = $(this);
        const title = event.find(".ecp-event-title").text().toLowerCase();
        const description = event
          .find(".ecp-event-description")
          .text()
          .toLowerCase();

        if (title.includes(searchTerm) || description.includes(searchTerm)) {
          event.show();
        } else {
          event.hide();
        }
      });
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

  // Exponer ECPPublic globalmente
  window.ECPPublic = ECPPublic;
})(jQuery);
