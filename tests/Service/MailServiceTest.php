<?php


namespace App\Tests\Service;


use App\Entity\User;
use App\Service\MailService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Mime\Exception\RfcComplianceException;

class MailServiceTest extends KernelTestCase
{
    /**
     * @var MailService
     */
    private $mailService;

    protected function setUp(): void
    {
        $mailerInterface = $this->getMockBuilder(MailerInterface::class)->disableOriginalConstructor()->getMock();
        $this->mailService = new MailService($mailerInterface);
    }

    public function testNotifySuccessfully()
    {
        $user = new User();
        $user->setEmail('email@email.com');

        $this->assertTrue($this->mailService->notifyUserEightItems($user));
    }

    public function testNotifyUserWithBadEmailAddress()
    {
        $this->expectException(RfcComplianceException::class);

        $this->mailService->notifyUserEightItems((new User())->setEmail('email'));
    }

    public function testNotifyUserWithUserWithoutEmailAddress()
    {

        $this->expectException(InvalidArgumentException::class);

        $this->mailService->notifyUserEightItems((new User()));
    }

}