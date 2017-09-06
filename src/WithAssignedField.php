<?php

namespace Verja;

trait WithAssignedField
{
    /** @var Field */
    protected $field;

    /**
     * Assign $this to $field
     *
     * Clones $this when already assigned.
     *
     * @param Field $field
     * @return $this
     */
    public function assign(Field $field)
    {
        if ($this->field !== null && $this->field !== $field) {
            // already assigned - clone me and assign $field to the clone
            $myField = $this->field;
            $this->field = null;
            $clone = clone $this;
            $this->field = $myField;

            return $clone->assign($field);
        }

        $this->field = $field;
        return $this;
    }
}
