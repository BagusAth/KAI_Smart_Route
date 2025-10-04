// /**
//  * KAI Guard - Main Frontend Script
//  * 
//  * This script initializes the behavior collector and handles UI interactions.
//  * It manages the trust score display, form submissions, and modal interactions.
//  */

// // Initialize the behavior collector when the DOM is loaded
// document.addEventListener('DOMContentLoaded', () => {
//   // DOM elements
//   const trustScoreElement = document.getElementById('trustScore');
//   const trustScoreFill = document.getElementById('trustScoreFill');
//   const statusMessage = document.getElementById('statusMessage');
//   const ticketForm = document.getElementById('ticketForm');
//   const modal = document.getElementById('actionModal');
//   const modalTitle = document.getElementById('modalTitle');
//   const modalMessage = document.getElementById('modalMessage');
//   const challengeContent = document.getElementById('challengeContent');
//   const modalPrimaryButton = document.getElementById('modalPrimaryButton');
//   const modalSecondaryButton = document.getElementById('modalSecondaryButton');
//   const closeModalButton = document.getElementById('closeModal');
  
//   // Set minimum date to today for the date picker
//   const dateInput = document.getElementById('date');
//   const today = new Date();
//   const formattedDate = today.toISOString().split('T')[0];
//   dateInput.setAttribute('min', formattedDate);
  
//   // Initialize the behavior collector
//   const collector = new BehaviorCollector({
//     debug: false,
//     sendInterval: 3000, // Send data every 3 seconds
//     endpoint: '/api/behavior/analyze',
//     onBotDetected: handleBotDetection,
//     onChallengeRequired: handleChallengeRequired
//   });
  
//   // Start collecting behavior data
//   collector.start();
  
//   /**
//    * Update the trust score display
//    * @param {Number} score - Trust score between 0-1
//    */
//   function updateTrustScore(score) {
//     // Format the score as a percentage
//     const percentage = Math.round(score * 100);
    
//     // Update the text
//     trustScoreElement.textContent = `${percentage}%`;
//     trustScoreElement.classList.add('pulse');
    
//     // Update the fill bar
//     trustScoreFill.style.width = `${percentage}%`;
    
//     // Set color based on score
//     if (score >= 0.8) {
//       trustScoreFill.style.backgroundColor = 'var(--success-color)';
//       trustScoreElement.style.color = 'var(--success-color)';
//     } else if (score >= 0.5) {
//       trustScoreFill.style.backgroundColor = 'var(--accent-color)';
//       trustScoreElement.style.color = 'var(--accent-color)';
//     } else {
//       trustScoreFill.style.backgroundColor = 'var(--secondary-color)';
//       trustScoreElement.style.color = 'var(--secondary-color)';
//     }
    
//     // Remove animation class after animation completes
//     setTimeout(() => {
//       trustScoreElement.classList.remove('pulse');
//     }, 500);
//   }
  
//   /**
//    * Handle form submission
//    * @param {Event} event - Form submit event
//    */
//   function handleFormSubmit(event) {
//     event.preventDefault();
    
//     // Show loading message
//     statusMessage.textContent = 'Mencari tiket tersedia...';
//     statusMessage.className = 'status-message';
//     statusMessage.classList.add('warning');
    
//     // Simulate API call delay
//     setTimeout(() => {
//       // Check if the user has a high enough trust score
//       const trustScore = parseFloat(trustScoreElement.textContent) / 100;
      
//       if (trustScore >= 0.5) {
//         // Success - show available tickets
//         statusMessage.textContent = 'Tiket tersedia! Silakan lanjutkan ke pembayaran.';
//         statusMessage.className = 'status-message';
//         statusMessage.classList.add('success');
//       } else {
//         // Error - blocked due to bot detection
//         statusMessage.textContent = 'Permintaan diblokir. Sistem mendeteksi perilaku tidak wajar.';
//         statusMessage.className = 'status-message';
//         statusMessage.classList.add('error');
//       }
//     }, 1500);
//   }
  
//   /**
//    * Handle bot detection
//    * @param {Object} result - Server response
//    */
//   function handleBotDetection(result) {
//     // Update the trust score
//     updateTrustScore(result.trustScore);
    
//     // Show the modal with block message
//     modalTitle.textContent = 'Akses Diblokir';
//     modalMessage.textContent = result.message || 'Sistem mendeteksi perilaku yang mencurigakan. Akses Anda telah dibatasi.';
    
//     // Clear any previous challenge content
//     challengeContent.innerHTML = '';
    
//     // Update button text
//     modalPrimaryButton.textContent = 'Coba Lagi';
//     modalSecondaryButton.textContent = 'Tutup';
    
//     // Show the modal
//     modal.classList.add('show');
//   }
  
//   /**
//    * Handle challenge required
//    * @param {Object} result - Server response
//    */
//   function handleChallengeRequired(result) {
//     // Update the trust score
//     updateTrustScore(result.trustScore);
    
//     // Show the modal with challenge
//     modalTitle.textContent = 'Verifikasi Tambahan';
//     modalMessage.textContent = result.message || 'Mohon selesaikan verifikasi tambahan untuk melanjutkan.';
    
//     // Create a simple challenge (in a real app, this would be more sophisticated)
//     challengeContent.innerHTML = `
//       <div class="challenge">
//         <p>Silakan pilih gambar yang menunjukkan kereta api:</p>
//         <div class="challenge-options">
//           <div class="challenge-option">
//             <img src="https://via.placeholder.com/100?text=Kereta" alt="Option 1">
//             <input type="radio" name="challenge" value="1">
//           </div>
//           <div class="challenge-option">
//             <img src="https://via.placeholder.com/100?text=Bus" alt="Option 2">
//             <input type="radio" name="challenge" value="2">
//           </div>
//           <div class="challenge-option">
//             <img src="https://via.placeholder.com/100?text=Pesawat" alt="Option 3">
//             <input type="radio" name="challenge" value="3">
//           </div>
//         </div>
//       </div>
//     `;
    
//     // Update button text
//     modalPrimaryButton.textContent = 'Verifikasi';
//     modalSecondaryButton.textContent = 'Batal';
    
//     // Show the modal
//     modal.classList.add('show');
//   }
  
//   /**
//    * Close the modal
//    */
//   function closeModal() {
//     modal.classList.remove('show');
//   }

//   function saveHumanSample() {
//     const data = collector._calculateMetrics();
    
//     fetch('http://localhost:5000/api/training/save', {
//       method: 'POST',
//       headers: { 'Content-Type': 'application/json' },
//       body: JSON.stringify({
//         data: data,
//         label: 1
//       })
//     })
//     .then(res => res.json())
//     .then(result => {
//       console.log("✅ Human sample saved:", result);
//     })
//     .catch(err => {
//       console.error("❌ Error saving human sample:", err);
//     });
    
//   }
  
//   /**
//    * Handle primary button click in modal
//    */
//   function handlePrimaryButtonClick() {
//     // Check if this is a challenge response
//     const challengeRadios = document.querySelectorAll('input[name="challenge"]');
//     if (challengeRadios.length > 0) {
//       let selected = false;
//       let correctAnswer = false;
      
//       challengeRadios.forEach(radio => {
//         if (radio.checked) {
//           selected = true;
//           // In this example, option 1 (kereta) is the correct answer
//           if (radio.value === '1') {
//             correctAnswer = true;
//           }
//         }
//       });
      
//       if (!selected) {
//         alert('Silakan pilih salah satu opsi.');
//         return;
//       }
      
//       if (correctAnswer) {
//         // Increase trust score for correct answer
//         updateTrustScore(0.8);
//         closeModal();
//       } else {
//         // Decrease trust score for wrong answer
//         updateTrustScore(0.3);
//         alert('Jawaban salah. Silakan coba lagi.');
//       }
//     } else {
//       // Just close the modal if it's not a challenge
//       closeModal();
//     }
//   }
  
//   // Event listeners
//   ticketForm.addEventListener('submit', (e) => {
//     handleFormSubmit(e);
//     saveHumanSample();
//   });  
//   closeModalButton.addEventListener('click', closeModal);
//   modalPrimaryButton.addEventListener('click', handlePrimaryButtonClick);
//   modalSecondaryButton.addEventListener('click', closeModal);
  
//   // Set initial trust score (this would normally come from the server)
//   // For demo purposes, we'll start with a medium trust score
//   setTimeout(() => {
//     updateTrustScore(0.6);
//   }, 2000);
// });

document.addEventListener('DOMContentLoaded', () => {
  // --- DOM Elements ---
  const trustScoreElement = document.getElementById('trustScore');
  const trustScoreFill = document.getElementById('trustScoreFill');
  const statusMessage = document.getElementById('statusMessage');
  const ticketForm = document.getElementById('ticketForm');
  const modal = document.getElementById('actionModal');
  const modalTitle = document.getElementById('modalTitle');
  const modalMessage = document.getElementById('modalMessage');

  // --- Initialize Services ---
  const clientModel = new ClientModelServiceNonTF();
  const collector = new BehaviorCollector({ debug: false });
  collector.start();

  // --- Main Logic ---
  
  // Periodically analyze behavior
  setInterval(async () => {
    const metrics = collector._calculateMetrics();
    let trustScore = null;

    // Prioritize client-side evaluation for speed
    if (clientModel.isReady()) {
      trustScore = await clientModel.evaluate(metrics);
    } else {
      // Fallback to server-side if client model isn't ready
      trustScore = await evaluateOnServer(metrics);
    }

    if (trustScore !== null) {
      updateTrustScore(trustScore);
    }
  }, 2000); // Analyze every 2 seconds

  /**
   * Updates the trust score display in the UI.
   * @param {number} score - Trust score between 0 and 1.
   */
  function updateTrustScore(score) {
    const percentage = Math.round(score * 100);
    trustScoreElement.textContent = `${percentage}%`;
    trustScoreFill.style.width = `${percentage}%`;

    if (score >= 0.4) {
      trustScoreFill.className = 'score-fill high';
    } else if (score >= 0.15) {
      trustScoreFill.className = 'score-fill medium';
    } else {
      trustScoreFill.className = 'score-fill low';
    }
  }

  /**
   * Fallback function to send data to the server for evaluation.
   * @param {Object} metrics - Calculated behavior metrics.
   * @returns {Promise<number|null>}
   */
  async function evaluateOnServer(metrics) {
    try {
      const response = await fetch('/api/behavior/analyze', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(metrics)
      });
      if (!response.ok) return null;
      const result = await response.json();
      if (result.success && result.analysis && typeof result.analysis.trustScore === 'number') {
        return result.analysis.trustScore;
      } else if (result.analysis && typeof result.analysis.trustScore === 'number') {
        return result.analysis.trustScore;
      }
      return null;
    } catch (error) {
      console.error('Server evaluation failed:', error);
      return null;
    }
  }

  /**
   * Handles the ticket search form submission.
   * @param {Event} event
   */
  async function handleFormSubmit(event) {
    event.preventDefault();
    const finalMetrics = collector._calculateMetrics();
    
    try {
      const response = await fetch('/api/behavior/analyze', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(finalMetrics)
      });
      
      if (!response.ok) {
        statusMessage.textContent = '⚠️ Terjadi kesalahan server. Silakan coba lagi.';
        statusMessage.className = 'status-message warning';
        return;
      }
      
      const data = await response.json();
      const result = data.analysis || data;
      
      if (!result || typeof result.trustScore !== 'number') {
        statusMessage.textContent = '⚠️ Terjadi kesalahan. Silakan coba lagi.';
        statusMessage.className = 'status-message warning';
        return;
      }
      
      updateTrustScore(result.trustScore);
      
      switch (result.action) {
        case 'ALLOW':
          statusMessage.textContent = '✅ Tiket tersedia! Anda akan diarahkan ke pembayaran.';
          statusMessage.className = 'status-message success';
          break;
        case 'CHALLENGE':
          statusMessage.textContent = '⚠️ Silakan selesaikan verifikasi untuk melanjutkan.';
          statusMessage.className = 'status-message warning';
          showModal('Verifikasi Tambahan', result.message);
          break;
        case 'BLOCK':
          statusMessage.textContent = '❌ Permintaan diblokir: Perilaku mencurigakan terdeteksi.';
          statusMessage.className = 'status-message error';
          showModal('Akses Diblokir', result.message);
          break;
        default:
          statusMessage.textContent = '⚠️ Status tidak dikenali. Silakan coba lagi.';
          statusMessage.className = 'status-message warning';
      }
    } catch (error) {
      console.error('Error submitting form:', error);
      statusMessage.textContent = '⚠️ Terjadi kesalahan. Silakan coba lagi.';
      statusMessage.className = 'status-message warning';
    }
  }

  /**
   * Displays the modal with a custom title and message.
   * @param {string} title
   * @param {string} message
   */
  function showModal(title, message) {
    modalTitle.textContent = title;
    modalMessage.textContent = message;
    
    // Add challenge content if this is a verification modal
    const challengeContent = document.getElementById('challengeContent');
    if (title.includes('Verifikasi')) {
      challengeContent.innerHTML = `
        <div class="challenge">
          <p>Silakan pilih gambar yang menunjukkan kereta api:</p>
          <div class="challenge-options">
            <div class="challenge-option">
              <img src="https://via.placeholder.com/100?text=Kereta" alt="Option 1">
              <input type="radio" name="challenge" value="1">
            </div>
            <div class="challenge-option">
              <img src="https://via.placeholder.com/100?text=Bus" alt="Option 2">
              <input type="radio" name="challenge" value="2">
            </div>
            <div class="challenge-option">
              <img src="https://via.placeholder.com/100?text=Pesawat" alt="Option 3">
              <input type="radio" name="challenge" value="3">
            </div>
          </div>
        </div>
      `;
    } else {
      challengeContent.innerHTML = '';
    }
    
    modal.classList.add('show');
  }

  // --- Event Listeners ---
  ticketForm.addEventListener('submit', handleFormSubmit);
  document.getElementById('closeModal').addEventListener('click', () => modal.classList.remove('show'));
  document.getElementById('modalSecondaryButton').addEventListener('click', () => modal.classList.remove('show'));
  document.getElementById('modalPrimaryButton').addEventListener('click', handlePrimaryButtonClick);
  
  /**
   * Handles the primary button click in the modal.
   */
  function handlePrimaryButtonClick() {
    // Check if this is a challenge response
    const challengeContent = document.getElementById('challengeContent');
    const challengeRadios = challengeContent.querySelectorAll('input[name="challenge"]');
    
    if (challengeRadios.length > 0) {
      let selected = false;
      let correctAnswer = false;
      
      challengeRadios.forEach(radio => {
        if (radio.checked) {
          selected = true;
          // In this example, option 1 (kereta) is the correct answer
          if (radio.value === '1') {
            correctAnswer = true;
          }
        }
      });
      
      if (!selected) {
        alert('Silakan pilih salah satu opsi.');
        return;
      }
      
      if (correctAnswer) {
        // Increase trust score for correct answer
        updateTrustScore(0.8);
        modal.classList.remove('show');
      } else {
        // Decrease trust score for wrong answer
        updateTrustScore(0.3);
        alert('Jawaban salah. Silakan coba lagi.');
      }
    } else {
      // Just close the modal if it's not a challenge
      modal.classList.remove('show');
    }
  }
});