(() => {
    const button = document.getElementById('ambient-toggle');
    const audio = document.getElementById('sea-ambience');
    const stateLabel = document.getElementById('ambient-state');
    if (!button || !audio || !stateLabel) return;

    audio.loop = true;
    audio.volume = 0.2;
    const preferenceKey = 'seaAmbience';
    let wantedOn = false;
    let playbackBlocked = false;
    try { wantedOn = localStorage.getItem(preferenceKey) === 'on'; } catch (_) { /* storage may be disabled */ }

    const setButton = (isOn, message = '') => {
        button.classList.toggle('is-on', isOn);
        button.setAttribute('aria-pressed', String(isOn));
        button.setAttribute('aria-label', isOn ? 'Turn ambient ocean sound off' : 'Turn ambient ocean sound on');
        button.title = message || `Ambient ocean sound is ${isOn ? 'on' : 'off'}`;
        stateLabel.textContent = isOn ? 'ON' : 'OFF';
    };
    const storePreference = (isOn) => {
        try { localStorage.setItem(preferenceKey, isOn ? 'on' : 'off'); } catch (_) { /* button still works */ }
    };
    const play = async () => {
        try {
            await audio.play();
            playbackBlocked = false;
            setButton(true);
        } catch (_) {
            playbackBlocked = true;
            setButton(true, 'Sound is enabled but playback was blocked. Click to retry.');
        }
    };

    setButton(wantedOn);
    if (wantedOn) play();

    button.addEventListener('click', async () => {
        if (wantedOn && playbackBlocked) {
            await play();
            return;
        }
        if (wantedOn) {
            wantedOn = false;
            audio.pause();
            audio.currentTime = 0;
            playbackBlocked = false;
            storePreference(false);
            setButton(false);
            return;
        }
        wantedOn = true;
        storePreference(true);
        setButton(true);
        await play();
    });
})();