<?php


namespace App\Tests\Entity;


use App\Entity\User;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * Class UserEntityTest
 * @package App\Tests\Entity
 */
class UserEntityTest extends KernelTestCase
{

    /**
     * @return User
     */
    public function getEntity(): User
    {
        return (new User())
            ->setEmail('email@email.com')
            ->setPassword('passwordWithMoreThan8characters' /* 31 Characters */)
            ->setLastname('lastname')
            ->setFirstname('firstname')
            ->setDob(new DateTime('- 13 years'));
    }

    public function assertHasErrors(User $user, int $number = 0)
    {
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($user);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity(), 0);
    }

    public function testValidEntityWithDobOlderThan13Years()
    {
        $this->assertHasErrors($this->getEntity()->setDob(new DateTime('- 20 years')), 0);
    }

    public function testInvalidEmail()
    {
        $this->assertHasErrors($this->getEntity()->setEmail('badFormat'), 1);
    }

    public function testBlankFirstname()
    {
        $this->assertHasErrors($this->getEntity()->setFirstname(''), 1);
        // No need to test if the first name is null because there is the strict mode
    }

    public function testBlankLastname()
    {
        $this->assertHasErrors($this->getEntity()->setLastname(''), 1);
        // No need to test if the first name is null because there is the strict mode
    }

    public function testYoungerThan13YearsOld()
    {
        $this->assertHasErrors($this->getEntity()->setDob(new DateTime(/* Now */)), 1);
    }
}