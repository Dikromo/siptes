var customInputmask = (function() {
    var config = {
        extendDefaults: {
        showMaskOnHover: false,
          showMaskOnFocus: false
          },
      extendDefinitions: {},
      extendAliases: {
          'numeric': {
            radixPoint: ',',
          groupSeparator: '.',
          autoGroup: true,
          placeholder: ''
        },
        'currency': {
            alias: 'numeric',
          digits: '*',
          digitsOptional: true,
            radixPoint: ',',
          groupSeparator: '.',
          autoGroup: true,
          placeholder: ''
        },
          'euro': {
            alias: 'currency',
          prefix: '',
          suffix: ' €',
            radixPoint: ',',
          groupSeparator: '',
          autoGroup: false,
        },
          'euroComplex': {
            alias: 'currency',
          prefix: '',
          suffix: ' €',
        }
      }
    };
  
      var init = function() {
          Inputmask.extendDefaults(config.extendDefaults);
      Inputmask.extendDefinitions(config.extendDefinitions);
      Inputmask.extendAliases(config.extendAliases);
      $('[data-inputmask]').inputmask();
      };
    
    return {
        init: init
    };
  }());
  
  // Initialize app.
  (function() {
      customInputmask.init();
  }());