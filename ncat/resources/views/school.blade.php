<!DOCTYPE html>
<html>
<head>
    <title>Select Schools</title>
</head>
<body>
    <h1>Select a School</h1>
    <form method="POST" action="/select-school">
        @csrf

        <label for="region">Region:</label>
        <select name="region" id="region" onchange="updateProvinces()">
            <option value="">Select a region</option>
            @foreach ($schoolsData as $region)
                <option value="{{ $region['region'] }}">{{ $region['region'] }}</option>
            @endforeach
        </select>

        <label for="province">Province:</label>
        <select name="province" id="province" onchange="updateMunicipalities()">
            <option value="">Select a province</option>
            <!-- Provinces will be populated via JavaScript -->
        </select>

        <label for="municipality">Municipality:</label>
        <select name="municipality" id="municipality" onchange="updateSchools()">
            <option value="">Select a municipality</option>
            <!-- Municipalities will be populated via JavaScript -->
        </select>

        <label for="school">School:</label>
        <select name="school" id="school">
            <option value="">Select a school</option>
            <!-- Schools will be populated via JavaScript -->
        </select>

        <button type="submit">Submit</button>
    </form>

    <script>
        const schoolsData = @json($schoolsData);

        function updateProvinces() {
            const regionSelect = document.getElementById('region');
            const provinceSelect = document.getElementById('province');
            const selectedRegion = regionSelect.value;

            provinceSelect.innerHTML = '<option value="">Select a province</option>';

            if (selectedRegion) {
                const region = schoolsData.find(r => r.region === selectedRegion);
                region.provinces.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.province;
                    option.textContent = province.province;
                    provinceSelect.appendChild(option);
                });
            }
        }

        function updateMunicipalities() {
            const regionSelect = document.getElementById('region');
            const provinceSelect = document.getElementById('province');
            const municipalitySelect = document.getElementById('municipality');
            const selectedRegion = regionSelect.value;
            const selectedProvince = provinceSelect.value;

            municipalitySelect.innerHTML = '<option value="">Select a municipality</option>';

            if (selectedRegion && selectedProvince) {
                const region = schoolsData.find(r => r.region === selectedRegion);
                const province = region.provinces.find(p => p.province === selectedProvince);
                province.municipalities.forEach(municipality => {
                    const option = document.createElement('option');
                    option.value = municipality.municipality;
                    option.textContent = municipality.municipality;
                    municipalitySelect.appendChild(option);
                });
            }
        }

        function updateSchools() {
            const regionSelect = document.getElementById('region');
            const provinceSelect = document.getElementById('province');
            const municipalitySelect = document.getElementById('municipality');
            const schoolSelect = document.getElementById('school');
            const selectedRegion = regionSelect.value;
            const selectedProvince = provinceSelect.value;
            const selectedMunicipality = municipalitySelect.value;

            schoolSelect.innerHTML = '<option value="">Select a school</option>';

            if (selectedRegion && selectedProvince && selectedMunicipality) {
                const region = schoolsData.find(r => r.region === selectedRegion);
                const province = region.provinces.find(p => p.province === selectedProvince);
                const municipality = province.municipalities.find(m => m.municipality === selectedMunicipality);
                municipality.schools.forEach(school => {
                    const option = document.createElement('option');
                    option.value = school.school_id;
                    option.textContent = school.school_name;
                    schoolSelect.appendChild(option);
                });
            }
        }
    </script>
</body>
</html>
