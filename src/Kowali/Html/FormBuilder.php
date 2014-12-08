<?php namespace Kowali\Html;

use Illuminate\Html\FormBuilder as Form;
use Illuminate\Support\MessageBag;

class FormBuilder extends Form {


    public function getErorrBag()
    {
        return $this->session->get('errors', new MessageBag);
    }

    public function group(\Closure $callback, $id, $label, array $attributes = [])
    {

        $error = $this->getErorrBag($id);

        $attributes = array_merge($attributes, ['class'=>'form-control']);

        if((isset($attributes['required']) && $attributes['required'] == true) || in_array('required', $attributes) )
        {
            $attributes['aria-required'] = true;
        }

        $html = $this->label($id, $label);

        if($error->has($id))
        {
            $error_id = "{$id}_error";
            $attributes['aria-invalid'] = 'true';
            if(isset($attributes['aria-labeledby']))
            {
                $attributes['aria-labeledby'] = "{$error_id} {$attributes['aria-labeledby']}";
            }
            else
            {
                $attributes['aria-labeledby'] = $error_id;
            }
            $html.= $callback($this, $id, $attributes);
            $html.= $error->first($id, "<span id='{$error_id}'class='feedback feedback-error'>:message</span>");
        }
        else
        {
            $html.= $callback($this, $id, $attributes);
        }

        return "<div class='form-group'>{$html}</div>";

    }

    public function textGroup($id, $label, array $attributes = [])
    {

        return $this->group(function($form, $id, $attributes){

            return $form->text($id, null, $attributes);

        }, $id, $label, $attributes);

    }


    public function emailGroup($id, $label, array $attributes = [])
    {

        return $this->group(function($form, $id, $attributes){

            return $form->email($id, null, $attributes);

        }, $id, $label, $attributes);

    }

    public function dateGroup($id, $label, array $attributes = [])
    {

        return $this->group(function($form, $id, $attributes){

            return $form->input('date', $id, null, $attributes);

        }, $id, $label, $attributes);

    }
    public function textareaGroup($id, $label, array $attributes = [])
    {

        return $this->group(function($form, $id, $attributes){

            return $form->textarea($id, null, $attributes);

        }, $id, $label, $attributes);

    }

    public function selectGroup($id, $label, $options, array $attributes = [])
    {

        return $this->group(function($form, $id, $attributes) use ($options){

            return $form->select($id, $options, null, $attributes);

        }, $id, $label, $attributes);

    }

    public function submitGroup($label, array $attributes = [])
    {
        $attributes['type'] = 'submit';

        $html = '<div class="form-group">';
        $html.= $this->button($label, $attributes);
        $html.= '</div>';

        return $html;
    }

    public function errors($key)
    {
        $errors = $this->getErorrBag();
        if($errors->has()) {
            $alert = "<strong>" . \Lang::choice($key, count($errors)) . "</strong>";
            $html = '';

            foreach($errors->all('<p><a href="#:key">:message</a></p>') as $message)
            {
                $html .= $message;
            }

            return "<div class='alert alert-danger'>{$alert}{$html}</div>";
        }
    }
}


