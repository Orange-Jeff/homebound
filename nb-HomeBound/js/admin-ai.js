// nb-HomeBound AI Generation JavaScript
// Version 1.2 - 2025-11-14 - Switched to v1 API endpoint and gemini-1.5-pro-latest model.

const AI_BASE_URL = 'https://generativelanguage.googleapis.com/v1/models/';
const GEMINI_MODEL = 'gemini-1.5-pro-latest';
const IMAGE_MODEL = 'gemini-2.5-flash-image';

// Status display
function showStatus(message, isError = false) {
    const statusEl = document.getElementById('ai-status');
    statusEl.textContent = message;
    statusEl.className = isError ? 'mt-3 text-center text-sm font-medium text-red-500' : 'mt-3 text-center text-sm font-medium text-accent';
}

// Get API key from config
async function getApiKey() {
    const response = await fetch('api-get-config.php');
    const config = await response.json();
    return config.api_key;
}

// Generate text content for a field
async function generateField(field, turnNumber = null) {
    showStatus(`Generating ${field}...`);

    try {
        const response = await fetch('api-generate-field.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ field, turn_number: turnNumber })
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById(field).value = data.content;
            showStatus(`✓ ${field} generated!`);
            setTimeout(() => showStatus(''), 2000);
        } else {
            showStatus(`Error: ${data.error}`, true);
        }
    } catch (error) {
        showStatus(`Error: ${error.message}`, true);
    }
}

// Generate complete day
async function generateFullDay() {
    const generateAudio = document.getElementById('generate-audio').checked;
    showStatus('🚀 Generating complete day story... This may take a minute...');

    try {
        const response = await fetch('api-generate-day.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ generate_audio: generateAudio })
        });

        const data = await response.json();

        if (data.success) {
            // Fill in all fields
            const dayData = data.data;

            document.getElementById('planet_name').value = dayData.planet_name || '';

            for (let turn = 1; turn <= 3; turn++) {
                document.getElementById(`turn${turn}_para1`).value = dayData[`turn${turn}_para1`] || '';
                document.getElementById(`turn${turn}_para2`).value = dayData[`turn${turn}_para2`] || '';
                document.getElementById(`turn${turn}_para3`).value = dayData[`turn${turn}_para3`] || '';
                document.getElementById(`turn${turn}_choice_a`).value = dayData[`turn${turn}_choice_a`] || '';
                document.getElementById(`turn${turn}_choice_b`).value = dayData[`turn${turn}_choice_b`] || '';
                document.getElementById(`turn${turn}_death_desc`).value = dayData[`turn${turn}_death_desc`] || '';

                if (dayData[`turn${turn}_image_url`]) {
                    document.getElementById(`turn${turn}_image_url`).value = dayData[`turn${turn}_image_url`];
                }

                if (dayData[`turn${turn}_audio_url`]) {
                    document.getElementById(`turn${turn}_audio_url`).value = dayData[`turn${turn}_audio_url`];
                }
            }

            document.getElementById('home_trip_desc').value = dayData.home_trip_desc || '';

            showStatus('✓ Complete day generated successfully!');
        } else {
            showStatus(`Error: ${data.error}`, true);
        }
    } catch (error) {
        showStatus(`Error: ${error.message}`, true);
    }
}

// Generate image for a turn
async function generateImage(turn) {
    showStatus(`Generating image for Turn ${turn}...`);

    try {
        // Build prompt from story content
        const para1 = document.getElementById(`turn${turn}_para1`).value;
        const para2 = document.getElementById(`turn${turn}_para2`).value;
        const planetName = document.getElementById('planet_name').value;

        let prompt = `Sci-fi space scene, dramatic lighting, detailed, high quality, `;

        if (planetName) {
            prompt += `alien planet ${planetName}, `;
        }

        if (para1) {
            // Extract key visual elements from paragraph
            prompt += para1.substring(0, 200);
        } else {
            prompt += `space adventure scene, turn ${turn}`;
        }

        const response = await fetch('api-generate-image.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ prompt, turn })
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById(`turn${turn}_image_url`).value = data.image_url;
            showStatus(`✓ Image generated for Turn ${turn}!`);
            setTimeout(() => showStatus(''), 2000);
        } else {
            showStatus(`Error: ${data.error}`, true);
        }
    } catch (error) {
        showStatus(`Error: ${error.message}`, true);
    }
}

// Generate audio for a turn
async function generateAudio(turn) {
    showStatus(`Generating audio for Turn ${turn}...`);

    try {
        const para1 = document.getElementById(`turn${turn}_para1`).value;
        const para2 = document.getElementById(`turn${turn}_para2`).value;
        const para3 = document.getElementById(`turn${turn}_para3`).value;

        const text = `${para1} ${para2} ${para3}`.trim();

        if (!text) {
            showStatus('Please generate story text first!', true);
            return;
        }

        const response = await fetch('api-generate-audio.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ text, turn })
        });

        const data = await response.json();

        if (data.success) {
            document.getElementById(`turn${turn}_audio_url`).value = data.audio_url;
            showStatus(`✓ Audio generated for Turn ${turn}!`);
            setTimeout(() => showStatus(''), 2000);
        } else {
            showStatus(`Error: ${data.error}`, true);
        }
    } catch (error) {
        showStatus(`Error: ${error.message}`, true);
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {

    // Test API button
    const testApiBtn = document.getElementById('test-api-btn');
    if (testApiBtn) {
        testApiBtn.addEventListener('click', async function() {
            const resultDiv = document.getElementById('test-api-result');
            resultDiv.innerHTML = '<span class="text-accent">🔍 Testing API connection...</span>';

            try {
                const response = await fetch('api-test-connection.php');
                const data = await response.json();

                if (data.success) {
                    resultDiv.innerHTML = `<span class="text-green-400">✓ ${data.message}</span>`;
                } else {
                    resultDiv.innerHTML = `<span class="text-red-400">✗ ${data.error}: ${data.details || ''}</span><br><small class="text-text-muted">${data.fix || ''}</small>`;
                }
            } catch (error) {
                resultDiv.innerHTML = `<span class="text-red-400">✗ Connection failed: ${error.message}</span>`;
            }
        });
    }

    // Generate full day button
    const generateDayBtn = document.getElementById('generate-full-day');
    if (generateDayBtn) {
        generateDayBtn.addEventListener('click', generateFullDay);
    }

    // Individual field AI buttons
    document.querySelectorAll('.ai-field-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const field = this.dataset.field;
            const turn = this.dataset.turn || null;
            generateField(field, turn);
        });
    });

    // Image generation buttons
    document.querySelectorAll('.ai-image-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const turn = parseInt(this.dataset.turn);
            generateImage(turn);
        });
    });

    // Audio generation buttons
    document.querySelectorAll('.ai-audio-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const turn = parseInt(this.dataset.turn);
            generateAudio(turn);
        });
    });
});
