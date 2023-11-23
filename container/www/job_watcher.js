let current_job = undefined
let old_executions = [];
let old_executions_html = [];

const main_container = document.getElementById('main_container');
const button_container = document.getElementById('button_container_outer');
const back_button = document.getElementById('back_button')
const cancel_button = document.getElementById('cancel_button')
const trigger_button = document.getElementById('trigger_button')
const power_button = document.getElementById('power_button')
const enabled_switch = document.getElementById('enabled_switch')

back_button.addEventListener("click", back);
cancel_button.addEventListener("click", cancel);
trigger_button.addEventListener("click", add_trigger);
enabled_switch.addEventListener("click", switch_enable_disable);

start_timer()

async function start_timer() {
    setInterval(load, 5000)
    load()
}

async function load() {
    if (current_job !== undefined) {
        load_job(current_job)
        return
    }
    if (!is_empty(query_param("job")) && is_empty(query_param("execution"))) {
        back_button.style.visibility = "hidden"
        back_button.style.display = "none"
        load_job(query_param("job"))
        return
    }

    const jobs = await get_jobs()
    console.info("Found jobs:", jobs)
    if (jobs.length <= 1 && is_empty(query_param("execution"))) {
        back_button.style.visibility = "hidden"
        back_button.style.display = "none"
    }
    if (jobs.length == 0) {
        load_job("")
    } else if (jobs.length == 1) {
        load_job(jobs[0]);
    } else {
        let html = ""
        for (i in jobs) {
            const job = jobs[i]
            html += '<div class="job_chooser" onclick="load_job(\'' + job + '\')">' + await get_title(job) + '</div>'
        }
        main_container.innerHTML = html
        button_container.style.visibility = "hidden"
    }
}

async function back() {
    if (!is_empty(query_param("execution"))) {
        window.location.href = "index.html"
    } else {
        current_job = undefined
        load()
    }    
}

async function cancel() {
    if (current_job !== undefined) {
        console.info("Cancel job: ", current_job)
        fetch('trigger.php?filename=cancel&job=' + current_job, { cache: "no-store" })
    }
}

async function switch_enable_disable() {
    const enabled = enabled_switch.checked ? "1" : "0"
    if (current_job !== undefined) {
        console.info("Enable job: ", current_job)
        fetch('trigger.php?filename=enabled&job=' + current_job + '&params=' + enabled, { cache: "no-store" })
    }
}

async function load_job(job) {
    console.info("Load job:", job)
    current_job = job
    const title = await get_title(job)
    const menu = await get_menu(job)
    document.getElementById('main_title').innerHTML = title
    document.getElementById('menu__box').innerHTML = menu
    document.title = title
    button_container.style.visibility = "visible"
    const tmp = await get_job_executions(job)
    const executions = tmp
        .filter(e => e !== 'title.txt')
        .sort((a, b) => b.localeCompare(a))
    console.info("Found executions:", executions)
    if (executions.length === 0) {
        main_container.innerHTML = "No executions found"
    } else if (arrays_are_equal(executions, old_executions)) {
        console.info("Job still running -> refresh job")
        console.info
        await refresh(job, executions.shift())
    } else {
        console.info("New Job running -> full refresh")
        console.info("Executions:", executions)
        console.info("Old:", old_executions)
        old_executions = executions
        await full_refresh(job)
    }
    //setTimeout(() => load_job(job), 5000);
}

async function full_refresh(job) {
    const current_job = old_executions[0]
    let html = ""
    for (i in old_executions) {
        const e = old_executions[i]
        if (e == current_job) {
            continue
        }
        const add = await parse_execution(job, e)
        console.info("add:", add)
        html += add
    }
    old_executions_html = html
    await refresh(job, current_job)
}

async function refresh(job, execution) {
    const current_job = await parse_execution(job, execution)
    console.info("Current HTML:", current_job)
    console.info("Old HTML:", old_executions_html)

    if (is_empty(query_param("execution"))) {
        main_container.innerHTML = '<table id="job_overview">' + current_job + old_executions_html + '</table>'
    } else {
        main_container.innerHTML = '<table id="job_overview">' + current_job + old_executions_html + '</table><br><center><button title="Back" class="square_button" id="second_back_button" onclick="back()"></button><center>'
    }
}

async function parse_execution(job, execution) {
    console.info("Parse", execution)
    const status = await parse_status('status/' + job + "/" + execution)
    const date = execution.split("_")[0]
    const time = execution.split("_")[1].split(".")[0]
    let cells = '<td class="timestamp">' + date + '<br>' + time + '</td>'
    let status_cell = '<td class="job_status job_status_executing"></td>'
    for (let i = 0; i < status.length; i++) {
        step = status[i]
        if (step.description == "START") {
            continue
        }
        let description = step.description
        let cls = "step_executing"
        const now = await get_time()
        console.info("Current server time:", now)
        let endtime = new Date(now)
        if (i + 1 < status.length) { // is it not the last line of the status file
            description = status[i + 1].description
            cls = "step_finished" // if it is not the last line, this step is finished
            endtime = status[i + 1].timestamp
        }

        if (description == "FAILED") {
            cells += '<td class="step step_failed">' + format_step(step, endtime) + '</td>'
            status_cell = '<td class="job_status job_status_failed"></td>'
            break;
        } else if (description == "END") {
            cells += '<td class="step step_finished">' + format_step(step, endtime) + '</td>'
            status_cell = '<td class="job_status job_status_finished"></td>'
            break;
        } else {
            cells += '<td class="step ' + cls + '">' + format_step(step, endtime) + '</td>'
        }
    }
    let html = '<tr class="job_row" onclick="window.location=\'logs/' + job + '/' + execution + '\'">'
    html += status_cell
    html += cells
    html += '</tr>'
    return html
}

function format_step(step, endtime) {
    const duration = (endtime - step.timestamp) / 1000
    let minutes = Math.floor(duration / 60)
    let seconds = duration - (minutes * 60) + "s"
    if (minutes == 0) {
        minutes = ""
    } else {
        minutes = minutes + "m "
    }
    return step.description + "<br>" + minutes + seconds
}

async function get_jobs() {
    const response = await fetch(`jobs.php`, { cache: "no-store" });
    return response.json()
}

// fetch server time, because JS-time is client time
async function get_time() {
    const respone = await fetch(`time.php`, { cache: "no-store" });
    return respone.text()
}

async function get_job_executions(job) {
    if (!is_empty(query_param("execution"))) {
        return [ query_param("execution") ]
    } else {
        const response = await fetch(`executions.php?job=${job}`, { cache: "no-store" });
        return await response.json();
    }
}

async function parse_status(path) {
    const response = await fetch(path, { cache: "no-store" });
    const text = await response.text();
    const lines = text.split('\n').filter(line => line.trim() !== '');

    const parsedData = lines.map(line => {
        const parts = line.split(':');
        const timestamp = parts[0].replace('_', ' ') + ':' + parts[1] + ':' + parts[2];
        const description = parts.slice(3).join(':');

        console.log("Timestamp: ", timestamp)
        return {
            timestamp: new Date(timestamp),
            description: description
        };
    });

    return parsedData;
}

async function get_title(job) {
    try {
        const response = await fetch('status/' + job + '/title.txt', { cache: "no-store" });
        const text = await response.text();
        const title = text.split('\n').filter(line => line.trim() !== '')[0].trim();
        return title
    } catch (e) {
        return "unnamed job"
    }
}

async function get_menu(job) {
    try {
        const response = await fetch('.' + job + '/menu.txt', { cache: "no-store" });
        const lines = await response.text();
        let menu = ""
        lines.split('\n').forEach(line => {
            if (line.trim() != "") {
                const caption = line.split("=>")[0]
                const link = line.split("=>")[1]
                menu += "<li><a class='menu__item' href='" + link.trim() + "'>" + caption.trim() + "</a></li>"    
            }            
        });        
        return menu
    } catch (e) {
        return "unnamed job"
    }
}


function query_param(name) {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    return urlParams.get(name);
}

function is_empty(value) {
    return typeof value !== 'string' || value.trim() === '';
}

function arrays_are_equal(array1, array2) {
    if (array1.length !== array2.length) {
        return false;
    }
    return array1.every((value, index) => value === array2[index]);
}

async function add_trigger(params = "") {
    if (current_job !== undefined) {
        if (params == "" && document.getElementById('params') != null) {
            params = document.getElementById('params').value
        }
        const params_param = is_empty(params) ? "" : "&params=" + params
        console.info("Add trigger for", current_job)
        await fetch('trigger.php?filename=trigger&job=' + current_job + params_param, { cache: "no-store" })
    }
}