<?php

include 'Step.php';

class Job
{
    private string $name;
    private Status $status;
    private DateTime $start;
    private ?DateTime $end;
    private array $steps = [];

    public function __construct(string $name, Status $status, DateTime $start, ?DateTime $end = null, array $steps = [])
    {
        $this->name = $name;
        $this->status = $status;
        $this->start = $start;
        $this->end = $end;
        $this->steps = $steps;
    }
    
    public function toHtml(): string
    {
        $html = '<tr class="job_row" onclick="window.location=\'log.html?file=' . $this->name . '\'">';
        $date = $this->start->format('Y-m-d');
        $time = $this->start->format('H:i:s');
        $html .= '<td class="job_status job_status_' . $this->status->value . '"></td>';
        $html .= '<td class="timestamp timestamp_' . $this->status->value . '">' . $date . '<br>' . $time . '</td>';
        foreach($this->steps as $step) {
            $html.= $step->toHtml();
        }
        return $html;
    }

    public function __toString(): string
    {
        $result = "Job: " . $this->start->format('Y-m-d H:i:s');
        
        if ($this->end !== null) {
            $result .= " - " . $this->end->format('Y-m-d H:i:s');
        } else {
            $result .= " - (running)";
        }
        
        $result .= "\n";
        
        $stepDescriptions = array_map(function($step) {
            return '"' . $step->__toString() . '"';
        }, $this->steps);
        
        $result .= "[ " . implode(", ", $stepDescriptions) . " ]";
        
        return $result;
    }

    
    public static function from(string $name, string $lines_string): Job
    {
        $lines = array_filter(array_map("trim", explode("\n", $lines_string)));
        $steps = [];
        $jobStart = null;
        $jobEnd = null;
        $status = Status::RUNNING;

        foreach ($lines as $i => $line) {
            if (!preg_match('/^(\d{4}-\d{2}-\d{2}_\d{2}:\d{2}:\d{2}):(.*)$/', $line, $matches)) {
                continue; // skip malformed line
            }

            $timeStr = $matches[1];
            $desc = trim($matches[2]);
            $date = DateTime::createFromFormat('Y-m-d_H:i:s', $timeStr);

            switch ($desc) {
                case 'START':
                    $jobStart = $date;
                    break;
                case 'END':
                    $jobEnd = $date;
                    $status = Status::SUCCESS;
                    break;
                case 'FAILED':
                    $jobEnd = $date;
                    $status = Status::ERROR;
                    break;
                default:
                    $steps[] = new Step($desc, Status::SUCCESS, $date, null);
                    break;
            }
        }

        // Assign end times to steps (each ends at the next step's start)
        for ($i = 0; $i < count($steps) - 1; $i++) {
            $steps[$i]->setEnd($steps[$i + 1]->getStart());
        }

        // Last step ends at job end (if available)
        if (!empty($steps)) {
            $lastStep = $steps[count($steps) - 1];
            $lastStep->setStatus($status);
            if ($jobEnd !== null) {
                $lastStep->setEnd($jobEnd);                
            }
        }

        return new Job($name, $status, $jobStart ?? new DateTime(), $jobEnd, $steps);
    }

}

enum Status : string
{
    case RUNNING = "executing";
    case SUCCESS = "finished";
    case ERROR = "failed";
}

?>
