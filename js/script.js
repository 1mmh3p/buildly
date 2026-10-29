// Navigation Toggle
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

if (hamburger) {
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
    });
}

// Close mobile menu when clicking on a link
document.querySelectorAll('.nav-link').forEach(n => n.addEventListener('click', () => {
    hamburger.classList.remove('active');
    navMenu.classList.remove('active');
}));

// Calculator Functionality
document.addEventListener('DOMContentLoaded', function() {
    const calculatorForm = document.getElementById('calculatorForm');
    const resultsSection = document.getElementById('resultsSection');
    const resultsTable = document.getElementById('resultsTable');
    const totalCostElement = document.getElementById('totalCost');
    const downloadPdfBtn = document.getElementById('downloadPdf');
    
    if (calculatorForm) {
        calculatorForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const area = parseFloat(document.getElementById('area').value);
            const buildingType = document.getElementById('buildingType').value;
            const floors = parseInt(document.getElementById('floors').value);
            const materialQuality = document.getElementById('materialQuality').value;
            
            // Calculate materials
            const materials = calculateMaterials(area, floors, materialQuality);
            
            // Display results
            displayResults(materials);
            
            // Show results section
            resultsSection.style.display = 'block';
            
            // Scroll to results
            resultsSection.scrollIntoView({ behavior: 'smooth' });
        });
    }
    
    // Calculate materials based on area and other factors
    function calculateMaterials(area, floors, quality) {
        // Base calculations per square meter
        let cementPerSqm = 0.15; // tons
        let steelPerSqm = 12; // kg
        let sandPerSqm = 0.5; // m³
        let bricksPerSqm = 50; // pieces
        let paintPerSqm = 0.2; // liters
        let tilesPerSqm = 1.1; // m²
        
        // Adjust for building type
        let buildingTypeMultiplier = 1;
        switch(buildingType) {
            case 'villa':
                buildingTypeMultiplier = 1.2;
                break;
            case 'apartment':
                buildingTypeMultiplier = 1.1;
                break;
            case 'basement':
                buildingTypeMultiplier = 0.9;
                break;
            default:
                buildingTypeMultiplier = 1;
        }
        
        // Adjust for material quality
        let qualityMultiplier = 1;
        switch(quality) {
            case 'premium':
                qualityMultiplier = 1.3;
                break;
            case 'luxury':
                qualityMultiplier = 1.6;
                break;
            default:
                qualityMultiplier = 1;
        }
        
        // Calculate total area (considering floors)
        const totalArea = area * floors;
        
        // Calculate quantities
        const cement = totalArea * cementPerSqm * buildingTypeMultiplier;
        const steel = totalArea * steelPerSqm * buildingTypeMultiplier;
        const sand = totalArea * sandPerSqm * buildingTypeMultiplier;
        const bricks = totalArea * bricksPerSqm * buildingTypeMultiplier;
        const paint = totalArea * paintPerSqm;
        const tiles = totalArea * tilesPerSqm;
        
        // Get prices (in a real app, these would come from a database)
        const prices = {
            cement: 1200,
            steel: 15,
            sand: 250,
            bricks: 2,
            paint: 50,
            tiles: 100
        };
        
        // Adjust prices for quality
        Object.keys(prices).forEach(material => {
            prices[material] = prices[material] * qualityMultiplier;
        });
        
        // Calculate costs
        const cementCost = cement * prices.cement;
        const steelCost = steel * prices.steel;
        const sandCost = sand * prices.sand;
        const bricksCost = bricks * prices.bricks;
        const paintCost = paint * prices.paint;
        const tilesCost = tiles * prices.tiles;
        
        const totalCost = cementCost + steelCost + sandCost + bricksCost + paintCost + tilesCost;
        
        return {
            materials: [
                { name: 'إسمنت', quantity: cement.toFixed(2), unit: 'طن', price: prices.cement.toFixed(2), cost: cementCost.toFixed(2) },
                { name: 'حديد', quantity: steel.toFixed(2), unit: 'كغ', price: prices.steel.toFixed(2), cost: steelCost.toFixed(2) },
                { name: 'رمل', quantity: sand.toFixed(2), unit: 'م³', price: prices.sand.toFixed(2), cost: sandCost.toFixed(2) },
                { name: 'طوب', quantity: bricks.toFixed(0), unit: 'قطعة', price: prices.bricks.toFixed(2), cost: bricksCost.toFixed(2) },
                { name: 'دهان', quantity: paint.toFixed(2), unit: 'لتر', price: prices.paint.toFixed(2), cost: paintCost.toFixed(2) },
                { name: 'سيراميك', quantity: tiles.toFixed(2), unit: 'م²', price: prices.tiles.toFixed(2), cost: tilesCost.toFixed(2) }
            ],
            totalCost: totalCost.toFixed(2)
        };
    }
    
    // Display results in the table
    function displayResults(results) {
        // Clear previous results
        resultsTable.innerHTML = '';
        
        // Add header
        const headerRow = document.createElement('tr');
        headerRow.innerHTML = `
            <th>المادة</th>
            <th>الكمية المطلوبة</th>
            <th>السعر للوحدة</th>
            <th>التكلفة الإجمالية</th>
        `;
        resultsTable.appendChild(headerRow);
        
        // Add materials
        results.materials.forEach(material => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${material.name}</td>
                <td>${material.quantity} ${material.unit}</td>
                <td>${material.price} $</td>
                <td>${material.cost} $</td>
            `;
            resultsTable.appendChild(row);
        });
        
        // Update total cost
        totalCostElement.textContent = `${results.totalCost} $`;
    }
    
    // PDF Download functionality
    if (downloadPdfBtn) {
        downloadPdfBtn.addEventListener('click', function() {
            // In a real implementation, this would generate a PDF
            alert('سيتم تنزيل التقرير بصيغة PDF. في التطبيق الكامل، ستتم هذه العملية باستخدام مكتبة مثل jsPDF.');
            
            // Simulate PDF download
            const element = document.createElement('a');
            element.setAttribute('href', 'data:text/plain;charset=utf-8,تقرير Buildly - هذا تقرير تجريبي. في التطبيق الكامل، سيكون هذا ملف PDF حقيقي.');
            element.setAttribute('download', 'تقرير-Buildly.pdf');
            element.style.display = 'none';
            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);
        });
    }
    
    // Contact Form Submission
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const message = document.getElementById('message').value;
            
            // In a real app, this would send data to the server
            // For now, just show a success message
            alert(`شكراً ${name} على رسالتك! سنرد عليك في أقرب وقت على ${email}.`);
            
            // Reset form
            contactForm.reset();
        });
    }
    
    // Admin Login Simulation
    const adminLoginForm = document.getElementById('adminLoginForm');
    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            // Simple validation (in real app, this would check against database)
            if (username === 'admin' && password === 'admin123') {
                alert('تم تسجيل الدخول بنجاح! سيتم توجيهك إلى لوحة التحكم.');
                // In real app, redirect to admin dashboard
                window.location.href = 'admin/dashboard.html';
            } else {
                alert('اسم المستخدم أو كلمة المرور غير صحيحة!');
            }
        });
    }
});

// Animation on scroll
function animateOnScroll() {
    const elements = document.querySelectorAll('.feature-card, .step, .decoration-card, .team-member');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    elements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(20px)';
        element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(element);
    });
}

// Initialize animations when DOM is loaded
document.addEventListener('DOMContentLoaded', animateOnScroll);


// دوال إضافية للميزات الجديدة

// دالة للاتصال بالشركة
function contactCompany(companyName) {
    alert(`سيتم التواصل مع شركة ${companyName} قريباً`);
    // يمكن إضافة منطق إرسال بريد إلكتروني أو رسالة هنا
}

// دالة لتحميل تقرير PDF
function downloadPdf() {
    // يمكن إضافة منطق إنشاء وتحميل PDF هنا
    alert('سيتم تحميل التقرير قريباً');
}

// دالة للطباعة
function printResults() {
    window.print();
}

// دالة لإظهار/إخفاء العناصر حسب المحافظة
function toggleProvinceElements(provinceId) {
    // يمكن إضافة منطق لإظهار/إخفاء عناصر معينة حسب المحافظة
    const elements = document.querySelectorAll('[data-province]');
    elements.forEach(element => {
        if (element.getAttribute('data-province') === provinceId) {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
    });
}

// دالة لحساب تكاليف الإكساء
function calculateFinishingCost(finishingType, area) {
    const costs = {
        'none': 0,
        'stone': 50,
        'marble': 80,
        'tiles': 30,
        'panels': 40,
        'full': 120
    };
    return costs[finishingType] * area;
}

// تهيئة الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // إضافة مستمعي الأحداث للأزرار الجديدة
    const downloadBtn = document.getElementById('downloadPdf');
    const printBtn = document.getElementById('printResults');
    
    if (downloadBtn) {
        downloadBtn.addEventListener('click', downloadPdf);
    }
    
    if (printBtn) {
        printBtn.addEventListener('click', printResults);
    }
    
    // تهيئة خيارات المحافظة
    const provinceSelect = document.getElementById('province');
    if (provinceSelect) {
        provinceSelect.addEventListener('change', function() {
            toggleProvinceElements(this.value);
        });
    }
});