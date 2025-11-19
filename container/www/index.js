const cancel_button = document.getElementById('cancel_button')
const trigger_button = document.getElementById('trigger_button')
const enabled_switch = document.getElementById('enabled_switch')

cancel_button.addEventListener("click", cancel);
trigger_button.addEventListener("click", add_trigger);
enabled_switch.addEventListener("click", switch_enable_disable);

async function cancel() {
    console.info("Cancel job")
    fetch('trigger.php?filename=cancel', { cache: "no-store" })
}

async function switch_enable_disable() {
    const enabled = enabled_switch.checked ? "1" : "0"
    console.info("Enable/Disable job")
    fetch('enable.php?&enabled=' + enabled, { cache: "no-store" })
}

async function add_trigger() {
    console.info("Add trigger")
    await fetch('trigger.php?filename=trigger' , { cache: "no-store" })
}

function startAutoRefresh() {
    setInterval(async () => {
        try {
            const response = await fetch('jobs.php', { cache: "no-store" });
            const content = await response.text();
            const mainContainer = document.getElementById('main_container');
            if (mainContainer) {
                mainContainer.innerHTML = content;
            }
        } catch (error) {
            console.error('Error refreshing content:', error);
        }
    }, 3000); // 3 seconds
}

document.addEventListener('DOMContentLoaded', startAutoRefresh);
