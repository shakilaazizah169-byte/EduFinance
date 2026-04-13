// resources/js/services/license-api.js

class LicenseAPI {
    constructor(baseUrl = '/api') {
        this.baseUrl = baseUrl;
    }

    async checkLicense(email) {
        const response = await fetch(`${this.baseUrl}/license/check`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ email })
        });
        
        return response.json();
    }

    async buyLicense(email, packageType, paymentMethod = 'qris') {
        const response = await fetch(`${this.baseUrl}/license/buy`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ 
                email, 
                package_type: packageType,
                payment_method: paymentMethod
            })
        });
        
        return response.json();
    }

    async getLicenseHistory(email) {
        const response = await fetch(`${this.baseUrl}/license/history/${email}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        return response.json();
    }

    async getMyStatus() {
        const response = await fetch(`${this.baseUrl}/license/my-status`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        return response.json();
    }
}

export default new LicenseAPI();