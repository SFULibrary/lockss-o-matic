<?php

/* 
 * The MIT License
 *
 * Copyright 2014. Michael Joyce <ubermichael@gmail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace LOCKSSOMatic\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use LOCKSSOMatic\UserBundle\Entity\User;

/**
 * Form type for creating and editing users.
 */
class AdminUserType extends AbstractType
{

    /**
     * Build a form.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // FOSUserBundle requires username and email, but we only want
        // email. So copy the email to the user name, and never show the
        // username field to the users.
        $builder
            ->addEventListener(FormEvents::BIND, function(FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();
                if (!$data instanceof User) {
                    return;
                }
                // just transfer the email field to the usernamefield
                $data->setUsername($data->getEmail());
            });
        $builder
            ->add('email')
            ->add('fullname')
            ->add('institution')
            ->add('roles', 'choice', array(
                'label' => 'Roles',
                'choices' => array(
                    'ROLE_ADMIN' => 'Admin',
                    'ROLE_LOMADMIN' => 'Pln Admin',
                ),
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ))
            ->add('enabled', 'choice', array(
                'label' => 'Account enabled',
                'choices' => array(
                    0 => 'No',
                    1 => 'Yes',
                ),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
        ));
    }

    /**
     * Bind the form to the User entity.
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LOCKSSOMatic\UserBundle\Entity\User'
        ));
    }

    /**
     * Get the name of the form.
     *
     * @return string
     */
    public function getName()
    {
        return 'lom_userbundle_user';
    }
}
