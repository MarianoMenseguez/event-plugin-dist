/**
 * JavaScript para integración con Divi
 */

(function ($) {
  "use strict";

  // Inicializar cuando el documento esté listo
  $(document).ready(function () {
    ECPDivi.init();
  });

  // Objeto para integración con Divi
  var ECPDivi = {
    // Inicializar
    init: function () {
      this.bindEvents();
      this.initDiviModules();
    },

    // Vincular eventos
    bindEvents: function () {
      // Eventos específicos de Divi
      $(document).on("et_pb_after_init", this.onDiviModuleInit);
      $(document).on("et_pb_before_init", this.onDiviModuleBeforeInit);
    },

    // Inicializar módulos de Divi
    initDiviModules: function () {
      // Verificar si estamos en el builder de Divi
      if (typeof et_pb !== "undefined") {
        this.registerDiviModules();
      }
    },

    // Registrar módulos de Divi
    registerDiviModules: function () {
      // Los módulos se registran automáticamente cuando se cargan las clases
      // Aquí podemos agregar lógica adicional si es necesario
    },

    // Evento cuando se inicializa un módulo de Divi
    onDiviModuleInit: function (e, module) {
      // Verificar si es uno de nuestros módulos
      if (module.module_class && module.module_class.includes("et_pb_ecp_")) {
        ECPDivi.handleECPModuleInit(module);
      }
    },

    // Evento antes de inicializar un módulo de Divi
    onDiviModuleBeforeInit: function (e, module) {
      // Verificar si es uno de nuestros módulos
      if (module.module_class && module.module_class.includes("et_pb_ecp_")) {
        ECPDivi.handleECPModuleBeforeInit(module);
      }
    },

    // Manejar inicialización de módulo ECP
    handleECPModuleInit: function (module) {
      // Agregar clases específicas para nuestros módulos
      module.$el.addClass("ecp-divi-module");

      // Inicializar funcionalidad específica según el tipo de módulo
      if (module.module_class.includes("et_pb_ecp_events")) {
        this.initEventsModule(module);
      } else if (module.module_class.includes("et_pb_ecp_event_card")) {
        this.initEventCardModule(module);
      } else if (module.module_class.includes("et_pb_ecp_upcoming_events")) {
        this.initUpcomingEventsModule(module);
      }
    },

    // Manejar antes de inicializar módulo ECP
    handleECPModuleBeforeInit: function (module) {
      // Preparar datos del módulo si es necesario
    },

    // Inicializar módulo de eventos
    initEventsModule: function (module) {
      const $el = module.$el;
      const settings = module.settings;

      // Agregar filtros si no existen
      if (!$el.find(".ecp-event-filters").length) {
        this.addEventFilters($el);
      }

      // Configurar layout
      this.setupEventLayout($el, settings);
    },

    // Inicializar módulo de tarjeta de evento
    initEventCardModule: function (module) {
      const $el = module.$el;
      const settings = module.settings;

      // Configurar tarjeta individual
      this.setupEventCard($el, settings);
    },

    // Inicializar módulo de próximos eventos
    initUpcomingEventsModule: function (module) {
      const $el = module.$el;
      const settings = module.settings;

      // Configurar layout horizontal/vertical
      this.setupUpcomingEventsLayout($el, settings);
    },

    // Agregar filtros de eventos
    addEventFilters: function ($container) {
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

      $container.prepend(filtersHtml);
    },

    // Configurar layout de eventos
    setupEventLayout: function ($container, settings) {
      const layout = settings.layout_style || "grid";
      const columns = settings.columns || "3";

      $container.attr("data-layout", layout);
      $container.attr("data-columns", columns);

      // Aplicar clases CSS
      $container.removeClass(
        "ecp-layout-grid ecp-layout-list ecp-layout-masonry"
      );
      $container.addClass("ecp-layout-" + layout);

      if (layout === "grid") {
        $container
          .find(".ecp-events-grid")
          .removeClass("ecp-grid-1 ecp-grid-2 ecp-grid-3 ecp-grid-4");
        $container.find(".ecp-events-grid").addClass("ecp-grid-" + columns);
      }
    },

    // Configurar tarjeta de evento
    setupEventCard: function ($container, settings) {
      // Configuración específica para tarjeta individual
      if (settings.show_registration === "off") {
        $container.find(".ecp-show-registration").hide();
      }
    },

    // Configurar layout de próximos eventos
    setupUpcomingEventsLayout: function ($container, settings) {
      const layout = settings.layout_style || "horizontal";

      $container.attr("data-layout", layout);
      $container.removeClass(
        "ecp-layout-horizontal ecp-layout-vertical ecp-layout-grid"
      );
      $container.addClass("ecp-layout-" + layout);
    },

    // Utilidades para Divi
    utils: {
      // Obtener configuración del módulo
      getModuleSettings: function (module) {
        return module.settings || {};
      },

      // Actualizar configuración del módulo
      updateModuleSettings: function (module, newSettings) {
        if (module.settings) {
          $.extend(module.settings, newSettings);
        }
      },

      // Refrescar módulo
      refreshModule: function (module) {
        if (module.refresh) {
          module.refresh();
        }
      },
    },
  };

  // Exponer ECPDivi globalmente
  window.ECPDivi = ECPDivi;
})(jQuery);
