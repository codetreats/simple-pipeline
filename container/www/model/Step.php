<?php

class Step
{
    private string $description;
    private Status $status;
    private DateTime $start;
    private ?DateTime $end;

    public function __construct(string $description, Status $status, DateTime $start, ?DateTime $end = null)
    {
        $this->description = $description;
        $this->status = $status;
        $this->start = $start;
        $this->end = $end;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function getEnd(): ?DateTime
    {
        return $this->end;
    }

    public function setEnd(DateTime $end): void
    {
        $this->end = $end;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    public function getDuration(): ?int
    {
        if ($this->end === null) {
            return (new DateTime())->getTimestamp() - $this->start->getTimestamp();
        }
        return $this->end->getTimestamp() - $this->start->getTimestamp();
    }

    public function format(): string
    {
        $duration = $this->getDuration();

        $minutesStr = '';
        $secondsStr = '';

        if ($duration !== null) {
            $minutes = intdiv($duration, 60);
            $seconds = $duration % 60;

            $minutesStr = $minutes > 0 ? $minutes . "m " : "";
            $secondsStr = $seconds . "s";
        }

        return $this->description
             . "<br>"
             . $minutesStr
             . $secondsStr;
    }

    public function toHtml(): string {
        return '<td class="step step_' . $this->status->value . '">' . $this->format() . '</td>';
    }

    public function __toString(): string
    {
        return str_replace("<br>", ": ", $this->format());
    }
}

?>