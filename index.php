<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการฐานข้อมูลศูนย์ช่วยเหลือผู้ประสบภัย อบจ.ศรีสะเกษ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #f0f2f5; }
        .shelter-card { transition: transform 0.2s, box-shadow 0.2s; }
        .shelter-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .modal { display: none; }
        .modal.is-open { display: flex; }
        .status-select { -webkit-appearance: none; -moz-appearance: none; appearance: none; padding-right: 1.5rem; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right 0.5rem center; background-repeat: no-repeat; background-size: 1em 1em; }
        .loader { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite; margin: 0 auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body class="text-gray-800">

    <!-- Add Shelter Modal -->
    <div id="addShelterModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full justify-center items-center z-50">
        <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <h3 class="text-xl leading-6 font-bold text-gray-900 text-center">เพิ่มศูนย์ช่วยเหลือใหม่</h3>
            <form id="addShelterForm" class="mt-4 space-y-3">
                <input type="text" id="shelterName" placeholder="ชื่อศูนย์" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" required>
                <input type="text" id="shelterCoordinator" placeholder="ชื่อผู้ประสานงาน" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" required>
                <input type="tel" id="shelterPhone" placeholder="เบอร์โทรติดต่อ" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" required>
                <select id="shelterAmphoe" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" required></select>
                <input type="number" id="shelterCapacity" placeholder="จำนวนที่รองรับได้" min="1" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" required>
                <div class="px-4 py-3 space-y-2"><button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">บันทึกข้อมูล</button><button id="closeModalBtn" type="button" class="w-full px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">ยกเลิก</button></div>
            </form>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal fixed inset-0 bg-gray-600 bg-opacity-50 justify-center items-center z-50">
        <div class="relative mx-auto p-6 w-full max-w-sm shadow-lg rounded-xl bg-white text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100"><svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div>
            <h3 class="text-lg font-bold mt-3">ยืนยันการลบ</h3>
            <p id="deleteConfirmText" class="text-sm text-gray-500 mt-2 px-7 py-3"></p>
            <div class="items-center px-4 py-3 space-x-2"><button id="confirmDeleteBtn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">ยืนยัน</button><button id="cancelDeleteBtn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">ยกเลิก</button></div>
        </div>
    </div>

    <div class="container mx-auto p-4 md:p-6 lg:p-8">
        <header class="text-center mb-8"><h1 class="text-3xl md:text-4xl font-bold text-indigo-800">ระบบจัดการศูนย์ช่วยเหลือผู้ประสบภัย</h1><p class="text-lg text-gray-600">องค์การบริหารส่วนจังหวัดศรีสะเกษ</p></header>

        <div class="mb-6 p-4 bg-white rounded-xl shadow-md flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
             <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center w-full md:w-auto">
                <div><p class="text-gray-500 text-sm">ศูนย์ทั้งหมด</p><p id="totalShelters" class="text-2xl font-bold text-indigo-600">-</p></div>
                <div><p class="text-gray-500 text-sm">ผู้พักพิงทั้งหมด</p><p id="totalOccupancy" class="text-2xl font-bold text-blue-600">-</p></div>
                <div><p class="text-gray-500 text-sm">รองรับได้ทั้งหมด</p><p id="totalCapacity" class="text-2xl font-bold text-green-600">-</p></div>
                <div><p class="text-gray-500 text-sm">คงเหลือ</p><p id="totalAvailable" class="text-2xl font-bold text-yellow-600">-</p></div>
            </div>
            <button id="openModalBtn" class="w-full md:w-auto flex-shrink-0 bg-indigo-600 text-white font-bold py-2 px-6 rounded-lg shadow-lg hover:bg-indigo-700"><span>...เพิ่มศูนย์ใหม่</span></button>
        </div>
        
        <div class="mb-6 p-4 bg-white rounded-xl shadow-md grid md:grid-cols-3 gap-4 items-end">
            <div><label class="text-sm font-medium">ค้นหาชื่อศูนย์</label><input type="text" id="searchInput" placeholder="พิมพ์ชื่อ..." class="mt-1 w-full px-3 py-2 border rounded-md"></div>
            <div><label class="text-sm font-medium">กรองตามอำเภอ</label><select id="amphoeFilter" class="mt-1 w-full px-3 py-2 border rounded-md"><option value="">ทุกอำเภอ</option></select></div>
            <div><button id="resetFilterBtn" class="w-full bg-gray-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-600">ล้างค่า</button></div>
        </div>

        <!-- ***** CHANGE HERE ***** -->
        <!-- Loading Indicator is now outside the list -->
        <div id="loading-indicator" class="text-center py-10" style="display: none;"><div class="loader"></div><p class="mt-2 text-gray-500">กำลังโหลดข้อมูล...</p></div>

        <div id="shelterList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Shelter cards will be inserted here -->
        </div>
    </div>

    <script>
        // =========================================================================
        // === CONFIGURATION: PASTE YOUR GOOGLE APPS SCRIPT URL HERE ===
        // =========================================================================
        const SCRIPT_URL = 'https://script.google.com/macros/s/AKfycbzpv7WoObP3HH2e82KHCyyIDejt24keoZVwADf7beyRip1_6sjXTmzV6FpCyk3XjYOplg/exec'; 
        // =========================================================================

        const SISAKET_AMPHOES = ["เมืองศรีสะเกษ", "กันทรลักษ์", "กันทรารมย์", "ขุขันธ์", "ขุนหาญ", "น้ำเกลี้ยง", "โนนคูณ", "บึงบูรพ์", "เบญจลักษ์", "ปรางค์กู่", "ปอย", "พยุห์", "ไพรบึง", "โพธิ์ศรีสุวรรณ", "ภูสิงห์", "เมืองจันทร์", "ยางชุมน้อย", "ราษีไศล", "วังหิน", "ศรีรัตนะ", "ศิลาลาด", "ห้วยทับทัน", "อุทุมพรพิสัย"].sort((a,b) => a.localeCompare(b, 'th'));

        let allShelters = []; 
        let shelterIdToDelete = null;

        const shelterList = document.getElementById('shelterList');
        const addShelterForm = document.getElementById('addShelterForm');
        const searchInput = document.getElementById('searchInput');
        const amphoeFilter = document.getElementById('amphoeFilter');
        
        async function apiCall(action, payload = {}) {
            const loadingIndicator = document.getElementById('loading-indicator');
            // Ensure indicator exists before trying to access its style
            if(loadingIndicator) loadingIndicator.style.display = 'block';

            try {
                if (action === 'read') {
                    const response = await fetch(`${SCRIPT_URL}?action=read`);
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return await response.json();
                } else {
                    const response = await fetch(SCRIPT_URL, {
                        method: 'POST',
                        mode: 'cors',
                        credentials: 'omit',
                        redirect: 'follow',
                        body: JSON.stringify({ action, payload })
                    });
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return await response.json();
                }
            } catch (error) {
                console.error('API Call Error:', error);
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับฐานข้อมูล: ' + error.message);
                return null;
            } finally {
                if(loadingIndicator) loadingIndicator.style.display = 'none';
            }
        }
        
        async function loadShelters() {
            shelterList.innerHTML = ''; // Clear old results before loading new ones
            const data = await apiCall('read');
            if(data) {
                if(Array.isArray(data)) {
                    allShelters = data.filter(s => s.id).sort((a, b) => a.name.localeCompare(b.name, 'th'));
                    filterAndRenderShelters();
                } else {
                    console.error('Received non-array data from API:', data);
                    allShelters = [];
                    filterAndRenderShelters();
                }
            }
        }

        function filterAndRenderShelters() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedAmphoe = amphoeFilter.value;
            const filtered = allShelters.filter(s => {
                const nameMatch = s.name && s.name.toLowerCase().includes(searchTerm);
                const amphoeMatch = !selectedAmphoe || s.amphoe === selectedAmphoe;
                return nameMatch && amphoeMatch;
            });
            renderShelters(filtered);
            updateSummary(filtered);
        }

        function renderShelters(shelters) {
            shelterList.innerHTML = '';
            if (shelters.length === 0) {
                 shelterList.innerHTML = `<p class="text-gray-500 col-span-full text-center py-10">ไม่พบข้อมูลศูนย์</p>`;
                 return;
            }
            const statusColors = { 'ต้องการ': 'bg-red-100', 'กำลังจัดส่ง': 'bg-yellow-100', 'ได้รับแล้ว': 'bg-green-100' };
            shelters.forEach(s => {
                const p = s.capacity > 0 ? (s.currentOccupancy / s.capacity) * 100 : 0;
                let c = p > 80 ? 'bg-red-500' : p > 50 ? 'bg-yellow-500' : 'bg-green-500';
                const card = document.createElement('div');
                card.className = 'shelter-card bg-white rounded-xl shadow-md p-5 flex flex-col space-y-3';
                card.innerHTML = `
                    <div><div class="flex justify-between items-start"><h2 class="text-xl font-bold break-words pr-2">${s.name}</h2><button class="delete-shelter-btn text-gray-400 hover:text-red-600 flex-shrink-0" data-id="${s.id}" data-name="${s.name}"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div><p class="text-sm text-gray-500">อ.${s.amphoe}</p></div>
                    <div class="pt-3 mt-3 border-t"><p><span class="font-semibold">ผู้ประสานงาน:</span> ${s.coordinator}</p><p><span class="font-semibold">โทร:</span> <a href="tel:${s.phone}" class="text-blue-600">${s.phone}</a></p></div>
                    <div class="pt-3 border-t"><div class="flex justify-between"><span>ผู้เข้าพัก</span><span>${s.currentOccupancy} / ${s.capacity}</span></div><div class="w-full bg-gray-200 h-4 rounded-full mt-1"><div class="${c} h-4 rounded-full" style="width:${p}%"></div></div><form class="update-occupancy-form flex space-x-2 mt-2"><input type="number" value="${s.currentOccupancy}" max="${s.capacity}" min="0" class="w-full border rounded-md p-1 text-center" required><input type="hidden" value="${s.id}"><button type="submit" class="px-3 bg-blue-500 text-white rounded-md">อัปเดต</button></form></div>
                    <div class="pt-3 border-t"><h3 class="font-medium">ความต้องการ</h3><div class="needs-list max-h-32 overflow-y-auto space-y-1 pr-2 mt-1">${(s.needs && s.needs.length) ? s.needs.map((n,i) => `<div class="flex items-center p-1 rounded-md ${statusColors[n.status] || ''}"><span class="flex-grow">${n.text}</span><select class="status-select text-xs rounded-md ml-2 p-1" data-id="${s.id}" data-index="${i}"><option ${n.status==='ต้องการ'?'selected':''}>ต้องการ</option><option ${n.status==='กำลังจัดส่ง'?'selected':''}>กำลังจัดส่ง</option><option ${n.status==='ได้รับแล้ว'?'selected':''}>ได้รับแล้ว</option></select><button class="delete-need-btn ml-2 text-gray-400 hover:text-red-500" data-id="${s.id}" data-index="${i}">X</button></div>`).join('') : '<p class="text-xs text-gray-400">ไม่มี</p>'}</div><form class="add-need-form flex space-x-2 mt-2"><input type="text" placeholder="เพิ่ม..." class="w-full border rounded-md p-1" required><input type="hidden" value="${s.id}"><button type="submit" class="px-3 bg-green-500 text-white rounded-md">เพิ่ม</button></form></div>`;
                shelterList.appendChild(card);
            });
        }
        function updateSummary(shelters) {
            const totalOccupancy = shelters.reduce((sum, s) => sum + Number(s.currentOccupancy || 0), 0);
            const totalCapacity = shelters.reduce((sum, s) => sum + Number(s.capacity || 0), 0);
            document.getElementById('totalShelters').textContent = shelters.length;
            document.getElementById('totalOccupancy').textContent = totalOccupancy;
            document.getElementById('totalCapacity').textContent = totalCapacity;
            document.getElementById('totalAvailable').textContent = totalCapacity - totalOccupancy;
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', () => {
            // Populate dropdowns
            [document.getElementById('shelterAmphoe'), amphoeFilter].forEach(sel => {
                SISAKET_AMPHOES.forEach(a => sel.add(new Option(a, a)));
            });
            amphoeFilter.options[0].textContent = "ทุกอำเภอ";

            // Initial load
            loadShelters();

            // Modals
            const addModal = document.getElementById('addShelterModal');
            const delModal = document.getElementById('deleteConfirmModal');
            document.getElementById('openModalBtn').addEventListener('click', () => addModal.classList.add('is-open'));
            document.getElementById('closeModalBtn').addEventListener('click', () => addModal.classList.remove('is-open'));
            document.getElementById('cancelDeleteBtn').addEventListener('click', () => delModal.classList.remove('is-open'));
            
            // Filters
            searchInput.addEventListener('input', filterAndRenderShelters);
            amphoeFilter.addEventListener('change', filterAndRenderShelters);
            document.getElementById('resetFilterBtn').addEventListener('click', () => { searchInput.value = ''; amphoeFilter.value = ''; filterAndRenderShelters(); });

            // Add Shelter Form
            addShelterForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const payload = {
                    name: document.getElementById('shelterName').value,
                    coordinator: document.getElementById('shelterCoordinator').value,
                    phone: document.getElementById('shelterPhone').value,
                    amphoe: document.getElementById('shelterAmphoe').value,
                    capacity: document.getElementById('shelterCapacity').value,
                    currentOccupancy: 0,
                    needs: []
                };
                const result = await apiCall('create', payload);
                if (result && result.status === 'success') {
                    addModal.classList.remove('is-open');
                    addShelterForm.reset();
                    await loadShelters();
                }
            });

            // Delete Shelter Confirmation
            document.getElementById('confirmDeleteBtn').addEventListener('click', async () => {
                if (shelterIdToDelete) {
                    const result = await apiCall('delete', { id: shelterIdToDelete });
                    if (result && result.status === 'success') {
                        delModal.classList.remove('is-open');
                        shelterIdToDelete = null;
                        await loadShelters();
                    }
                }
            });

            // Dynamic event listeners for cards
            shelterList.addEventListener('click', async e => {
                const target = e.target;
                if (target.closest('.delete-shelter-btn')) {
                    const btn = target.closest('.delete-shelter-btn');
                    shelterIdToDelete = btn.dataset.id;
                    document.getElementById('deleteConfirmText').innerHTML = `คุณแน่ใจว่าต้องการลบศูนย์<br><strong>"${btn.dataset.name}"</strong>?`;
                    delModal.classList.add('is-open');
                }
                if (target.closest('.delete-need-btn')) {
                    const btn = target.closest('.delete-need-btn');
                    e.stopPropagation(); // Prevent other events from firing
                    const { id, index } = btn.dataset;
                    const shelter = allShelters.find(s => s.id === id);
                    if (shelter) {
                        shelter.needs.splice(index, 1);
                        await apiCall('update', { id, needs: shelter.needs });
                        await loadShelters();
                    }
                }
            });

            shelterList.addEventListener('submit', async e => {
                e.preventDefault();
                const form = e.target;
                if(form.classList.contains('update-occupancy-form')) {
                    const id = form.querySelector('input[type=hidden]').value;
                    const occupancy = form.querySelector('input[type=number]').value;
                    await apiCall('update', { id, currentOccupancy: occupancy });
                    await loadShelters();
                }
                if(form.classList.contains('add-need-form')) {
                    const id = form.querySelector('input[type=hidden]').value;
                    const text = form.querySelector('input[type=text]').value.trim();
                    if(text) {
                        const shelter = allShelters.find(s => s.id === id);
                        const newNeeds = shelter.needs || [];
                        newNeeds.push({text, status: 'ต้องการ'});
                        await apiCall('update', { id, needs: newNeeds });
                        await loadShelters();
                    }
                }
            });

            shelterList.addEventListener('change', async e => {
                 if (e.target.classList.contains('status-select')) {
                    const sel = e.target;
                    const { id, index } = sel.dataset;
                    const shelter = allShelters.find(s => s.id === id);
                    if(shelter) {
                        shelter.needs[index].status = sel.value;
                        await apiCall('update', { id, needs: shelter.needs });
                        await loadShelters();
                    }
                }
            });
        });
    </script>
</body>
</html>
