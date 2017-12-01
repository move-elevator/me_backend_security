<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Factory\LoginProviderRedirectFactory;
use MoveElevator\MeBackendSecurity\Factory\PasswordChangeRequestFactory;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\PasswordChangeRequestFixture;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Rsaauth\RsaEncryptionDecoder;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class PasswordChangeRequestFactoryTest extends TestCase
{
    use PasswordChangeRequestFixture;

    protected $rsaEncryptionDecoder;

    public function setup()
    {
        $this->rsaEncryptionDecoder = $this->getMockBuilder(RsaEncryptionDecoder::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->setMethods(['decrypt'])
            ->getMock();

        $this->rsaEncryptionDecoder
            ->method('decrypt')
            ->will($this->returnArgument(0));
    }

    public function testCreateObjectFromValidArguments()
    {
        $rawPasswordChangeRequest = $this->getRawPasswordChangeRequestFixture();
        $expectedPasswordChangeRequest = $this->getPasswordChangeRequestFixture();

        $passwordChangeRequest = PasswordChangeRequestFactory::create(
            $rawPasswordChangeRequest,
            $this->rsaEncryptionDecoder
        );

        $this->assertEquals($passwordChangeRequest->getPassword(), $expectedPasswordChangeRequest->getPassword());
        $this->assertEquals(
            $passwordChangeRequest->getPasswordConfirmation(),
            $expectedPasswordChangeRequest->getPasswordConfirmation()
        );
    }
}
