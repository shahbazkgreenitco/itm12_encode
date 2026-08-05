<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('itm_network_inventory_basic_azure', function (Blueprint $table) {
            $table->increments('id')->index('id_basic_azure_idx');
            $table->string('BIOSSerialNumber', 100)->nullable()->index('biosserialnumber_basic_azure_idx');
            $table->string('ComputerName', 191)->nullable();
            $table->string('ComputerManufacturer', 100)->nullable();
            $table->string('ComputerModel', 100)->nullable();
            $table->string('ComputerDomain', 100)->nullable();
            $table->string('ComputerWorkgroup', 100)->nullable();
            $table->string('ComputerSystemType', 100)->nullable();
            $table->string('OSCaption', 191)->nullable();
            $table->string('OSManufacturer', 100)->nullable();
            $table->string('OSSerialNumber', 191)->nullable();
            $table->string('OSKey', 191)->nullable();
            $table->string('OSVersion', 100)->nullable();
            $table->string('OSServicePack', 100)->nullable();
            $table->string('OSArchitecture', 100)->nullable();
            $table->string('OSSystemDrive', 100)->nullable();
            $table->string('OSSystemDirectory', 100)->nullable();
            $table->string('ProcessorName', 100)->nullable();
            $table->string('ProcessorManufacturer', 100)->nullable();
            $table->string('ProcessorArchitecture', 100)->nullable();
            $table->string('ProcessorFamily', 100)->nullable();
            $table->string('ProcessorProcessorId', 100)->nullable();
            $table->string('ProcessorNumberOfCores', 100)->nullable();
            $table->string('IPv4', 30)->nullable();
            $table->string('ActiveMACAddress', 60)->nullable();
            $table->string('BIOSSMBIOSBIOSVersion', 100)->nullable();
            $table->string('BIOSManufacturer', 100)->nullable();
            $table->string('BIOSReleaseDate', 100)->nullable();
            $table->string('ProcessorThreadCount', 10)->nullable();
            $table->string('ProcessorMaxClockSpeed', 20)->nullable();
            $table->decimal('HddSize', 30)->nullable();
            $table->decimal('RamSize', 30)->nullable();
            $table->enum('platform', ['Windows', 'Linux/Unix', 'iOS'])->nullable();
            $table->integer('company')->nullable();
            $table->string('oui', 191)->nullable();
            $table->string('description', 191)->nullable();
            $table->string('productclass', 191)->nullable();
            $table->integer('is_dupe')->nullable();
            $table->integer('is_virtual')->nullable();
            $table->unsignedInteger('device_id')->nullable()->index('device_id_basic_azure_idx');
            $table->string('managedDeviceOwnerType', 191)->nullable();
            $table->dateTime('enrolledDateTime')->nullable();
            $table->dateTime('lastSyncDateTime')->nullable();
            $table->string('complianceState', 191)->nullable();
            $table->string('jailBroken', 191)->nullable();
            $table->string('managementAgent', 191)->nullable();
            $table->boolean('easActivated')->nullable();
            $table->string('easDeviceId', 191)->nullable();
            $table->dateTime('easActivationDateTime')->nullable();
            $table->boolean('azureADRegistered')->nullable();
            $table->string('deviceEnrollmentType', 191)->nullable();
            $table->string('activationLockBypassCode', 191)->nullable();
            $table->string('emailAddress', 191)->nullable();
            $table->string('azureADDeviceId', 191)->nullable();
            $table->string('deviceRegistrationState', 191)->nullable();
            $table->string('deviceCategoryDisplayName', 191)->nullable();
            $table->boolean('isSupervised')->nullable();
            $table->dateTime('exchangeLastSuccessfulSyncDateTime')->nullable();
            $table->string('exchangeAccessState', 191)->nullable();
            $table->string('exchangeAccessStateReason', 191)->nullable();
            $table->string('remoteAssistanceSessionUrl', 191)->nullable();
            $table->string('remoteAssistanceSessionErrorDetails', 191)->nullable();
            $table->boolean('isEncrypted')->nullable();
            $table->string('userPrincipalName', 191)->nullable();
            $table->string('imei', 191)->nullable();
            $table->dateTime('complianceGracePeriodExpirationDateTime')->nullable();
            $table->string('phoneNumber', 191)->nullable();
            $table->string('androidSecurityPatchLevel', 191)->nullable();
            $table->string('userDisplayName', 191)->nullable();
            $table->string('deviceHealthAttestationState', 191)->nullable();
            $table->string('subscriberCarrier', 191)->nullable();
            $table->string('meid', 191)->nullable();
            $table->string('partnerReportedThreatState', 191)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // $table->primary(['id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm_network_inventory_basic_azure');
    }
};
