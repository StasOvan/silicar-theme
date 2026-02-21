jQuery(document).ready(function($) {
    var autoData = null;
    var $brand = $('#auto_brand');
    var $model = $('#auto_model');
    var $yearFrom = $('#auto_year_from');
    var $yearTo = $('#auto_year_to');

    // Загружаем JSON
    $.getJSON(autoMetaData.jsonUrl, function(data) {
        autoData = data;
        // Если уже выбрана марка (например, при редактировании), загружаем модели
        if ($brand.val()) {
            loadModels($brand.val());
        }
    });

    // При изменении марки загружаем модели
    $brand.on('change', function() {
        var brandId = $(this).val();
        if (brandId && autoData) {
            loadModels(brandId);
        } else {
            $model.html('<option value="">Сначала выберите марку</option>').prop('disabled', true);
            $yearFrom.html('<option value="">Сначала выберите модель</option>').prop('disabled', true);
            $yearTo.html('<option value="">Сначала выберите модель</option>').prop('disabled', true);
        }
    });

    // При изменении модели загружаем годы
    $model.on('change', function() {
        var modelId = $(this).val();
        if (modelId && autoData) {
            loadYears(modelId);
        } else {
            $yearFrom.html('<option value="">Выберите год</option>').prop('disabled', true);
            $yearTo.html('<option value="">Выберите год</option>').prop('disabled', true);
        }
    });

    function loadModels(brandId) {
        var brand = autoData.find(function(b) { return b.id === brandId; });
        if (!brand) return;

        var options = '<option value="">Выберите модель</option>';
        brand.models.forEach(function(model) {
            options += '<option value="' + model.id + '">' + model.name + '</option>';
        });
        $model.html(options).prop('disabled', false);

        // Если есть текущая модель (переменная из PHP), выбираем её
        if (typeof currentModel !== 'undefined' && currentModel) {
            $model.val(currentModel).trigger('change');
        } else {
            // Иначе просто очищаем годы
            $yearFrom.html('<option value="">Выберите год</option>').prop('disabled', true);
            $yearTo.html('<option value="">Выберите год</option>').prop('disabled', true);
        }
    }

    function loadYears(modelId) {
        var model = null;
        for (var i = 0; i < autoData.length; i++) {
            var found = autoData[i].models.find(function(m) { return m.id === modelId; });
            if (found) {
                model = found;
                break;
            }
        }
        if (!model) return;

        var yearFrom = model['year-from'];
        var yearTo = model['year-to'] || new Date().getFullYear();

        var optionsFrom = '<option value="">От</option>';
        var optionsTo = '<option value="">До</option>';
        for (var y = yearFrom; y <= yearTo; y++) {
            optionsFrom += '<option value="' + y + '">' + y + '</option>';
            optionsTo += '<option value="' + y + '">' + y + '</option>';
        }
        $yearFrom.html(optionsFrom).prop('disabled', false);
        $yearTo.html(optionsTo).prop('disabled', false);

        // Если есть текущие значения, выбираем их
        if (typeof currentYearFrom !== 'undefined' && currentYearFrom) {
            $yearFrom.val(currentYearFrom);
        }
        if (typeof currentYearTo !== 'undefined' && currentYearTo) {
            $yearTo.val(currentYearTo);
        }
    }
});