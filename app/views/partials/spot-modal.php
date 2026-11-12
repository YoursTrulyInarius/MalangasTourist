<!-- Spot Details Modal -->
<div id="spotModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(13, 41, 23, 0.65); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(5px); transition: opacity 0.3s ease; opacity: 0;">
    <div id="spotModalContent" style="background: white; width: 90%; max-width: 1000px; height: 90vh; border-radius: 16px; display: flex; flex-direction: column; overflow: hidden; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid rgba(26, 77, 46, 0.15); transform: scale(0.9) translateY(40px); opacity: 0; transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;">
        <div style="padding: 1.2rem 1.75rem; border-bottom: 3px solid var(--secondary-color); display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #123821 0%, #1a5c35 100%); color: white;">
            <h3 style="margin: 0; color: white; font-size: 1.35rem; font-weight: 800; letter-spacing: 0.02em;">Spot Details</h3>
            <button onclick="closeSpotModal()" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.25); font-size: 1.1rem; color: rgba(255,255,255,0.9); cursor: pointer; line-height: 1; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div style="flex-grow: 1; position: relative;">
            <div id="modalLoader" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: white; z-index: 10;">
                <i class="fa-solid fa-circle-notch fa-spin" style="font-size: 3rem; color: var(--primary-color);"></i>
            </div>
            <iframe id="spotModalIframe" src="" style="width: 100%; height: 100%; border: none; display: none;" allow="geolocation"></iframe>
        </div>
    </div>
</div>

<script>
    // Modal Logic
    function openSpotModal(spotId) {
        const modal = document.getElementById('spotModal');
        const modalContent = document.getElementById('spotModalContent');
        const iframe = document.getElementById('spotModalIframe');
        const loader = document.getElementById('modalLoader');
        
        // Show modal and loader
        modal.style.display = 'flex';
        loader.style.display = 'flex';
        iframe.style.display = 'none';
        
        // Set iframe src with modal=1 parameter
        const baseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : '<?= BASE_URL ?>';
        iframe.src = baseUrl + '/spot/' + spotId + '?modal=1';
        
        // Trigger animations
        setTimeout(() => {
            modal.style.opacity = '1';
            modalContent.style.opacity = '1';
            modalContent.style.transform = 'scale(1) translateY(0)';
        }, 15);

        // Hide loader when iframe loads
        iframe.onload = function() {
            loader.style.display = 'none';
            iframe.style.display = 'block';
        };
        
        // Prevent body scrolling
        document.body.style.overflow = 'hidden';
    }

    function closeSpotModal() {
        const modal = document.getElementById('spotModal');
        const modalContent = document.getElementById('spotModalContent');
        const iframe = document.getElementById('spotModalIframe');
        
        // Trigger close animations
        modal.style.opacity = '0';
        modalContent.style.opacity = '0';
        modalContent.style.transform = 'scale(0.9) translateY(40px)';
        
        setTimeout(() => {
            modal.style.display = 'none';
            iframe.src = '';
        }, 300);
        
        // Restore body scrolling
        document.body.style.overflow = '';
    }

    // Close modal when clicking outside
    document.getElementById('spotModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSpotModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('spotModal');
            if (modal.style.display === 'flex' || modal.style.opacity === '1') {
                closeSpotModal();
            }
        }
    });
</script>
