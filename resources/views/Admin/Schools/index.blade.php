@extends('layouts.default')

@section('content')
<div class="col-12 content-card">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title text-white">School List</h3>
            <div class="card-tools">
                <a href="" id="add" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus text-white"></i></a>
            </div>
        </div>
        <div class="card-body">
            <table id="schools-table" class="table table-bordered table-hover table-striped">
                <thead>
                    <tr>
                        <th>School</th>
                        <th>City</th>
                        <th>Provice</th>
                        <th>Region</th>
                        <th>Options</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="schools-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="schools-form">
                <div class="modal-body">
                    <div class="form-group">
                        
                        <label for="regionSelect">Select Region:</label>
                        <select class="form-control"  id="regionSelect">
                            <option value="">Select a region</option>
                        </select>

                        <label for="provinceSelect">Select Province:</label>
                        <select class="form-control"  id="provinceSelect" disabled>
                            <option value="">Select a province</option>
                        </select>

                        <label for="citySelect">Select City:</label>
                        <select class="form-control" name="city_id" id="citySelect" disabled>
                            <option value="">Select a city</option>
                        </select>
                        
                        <label>School Name</label>
                        <input type="text" name="school_name"class="form-control">
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="save">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="user-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body center">
                <div id="qrcode" style="text-align: center;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Fetch data and populate the regions dropdown
        $.getJSON('regions', function(data) {
            var $regionSelect = $('#regionSelect');
            $.each(data, function(index, region) {
                $regionSelect.append($('<option>', {
                    value: region.id,
                    text: region.name
                }));
            });

            // Handle region change
            $regionSelect.change(function() {
                var selectedRegionId = $(this).val();
                var $provinceSelect = $('#provinceSelect');
                var $citySelect = $('#citySelect');

                $provinceSelect.empty().append('<option value="">Select a province</option>');
                $citySelect.empty().append('<option value="">Select a city</option>').prop('disabled', true);

                if (selectedRegionId) {
                    var selectedRegion = data.find(function(region) {
                        return region.id == selectedRegionId;
                    });

                    $.each(selectedRegion.provinces, function(index, province) {
                        $provinceSelect.append($('<option>', {
                            value: province.id,
                            text: province.name
                        }));
                    });

                    $provinceSelect.prop('disabled', false);
                } else {
                    $provinceSelect.prop('disabled', true);
                }
            });

            // Handle province change
            $('#provinceSelect').change(function() {
                var selectedRegionId = $('#regionSelect').val();
                var selectedProvinceId = $(this).val();
                var $citySelect = $('#citySelect');

                $citySelect.empty().append('<option value="">Select a city</option>');

                if (selectedRegionId && selectedProvinceId) {
                    var selectedRegion = data.find(function(region) {
                        return region.id == selectedRegionId;
                    });
                    var selectedProvince = selectedRegion.provinces.find(function(province) {
                        return province.id == selectedProvinceId;
                    });

                    $.each(selectedProvince.cities, function(index, city) {
                        $citySelect.append($('<option>', {
                            value: city.id,
                            text: city.name
                        }));
                    });

                    $citySelect.prop('disabled', false);
                } else {
                    $citySelect.prop('disabled', true);
                }
            });
        }).fail(function() {
            console.error('Error fetching regions data.');
        });
    });
</script>


<script src="{{asset('js/Schools.js')}}"></script>
@endsection