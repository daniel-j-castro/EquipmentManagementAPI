# EquipmentManagementAPI
## API ENDPOINTS
CreateDeviceType
CreateManufacturer
CreateDevice
ReadDevice
ViewDeviceFiles
UpdateDevice
UploadFile
SearchDevices
DeleteDevice
## Examples, Instructions, and Expected Output of Endpoints
### ALL MANUFACTURER PARAMETERS ARE CASE SENSITIVE
---------------------------------------------------------------------------------------------------------------------------------------------

https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?CreateDeviceType
### CreateDeviceType
Creates a device type. Parameters (type=<device to insert>) full example of endpoint https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?CreateDeviceType&type=desk

#### JSON output on successful read
Status	"Success"
MSG	"Device Type desk successfully created!"
Device Type	"desk"

---------------------------------------------------------------------------------------------------------------------------------------------
### CreateManufacturer
https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?CreateManufacturer

Creates a manufacturer. Parameters (manu=<manufacturer to insert>) full example of endpoint https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?CreateManufacturer&manu=Toyota

#### JSON output on successful read
Status	"Success"
MSG	"Manufacturer Toyota successfully created!"
Manufacturer	"Toyota"

---------------------------------------------------------------------------------------------------------------------------------------------
### CreateDevice
https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?CreateDevice

Creates a device. Parameters (sn=<Serial number for device (accepts with "SN-" and without)>,manu=<manufacturer for device>,type=<type for device>) full example of endpoint https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?CreateDevice&sn=SN-000010c0d5d5c8c69b924fb706d6a24p&manu=HP&type=computer

#### JSON output on successful read
Status	"Success"
MSG	"Device SN-000010c0d5d5c8c69b924fb706d6a24p successfully created!"
Serial Number	"SN-000010c0d5d5c8c69b924fb706d6a24p"
Device Type	"computer"
Manufacturer	"HP"

---------------------------------------------------------------------------------------------------------------------------------------------
### ReadDevice
https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ReadDevice

Reads a device from the database. Parameters(sn=<Serial number of device to read>) full example of endpoint https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ReadDevice&sn=SN-000010c0d5d5c8c69b924fb706d6a24e
#### JSON output on successful read

Status	"Success"
MSG	"Successfully read device SN-000010c0d5d5c8c69b924fb706d6a24e."
Serial Number	"SN-000010c0d5d5c8c69b924fb706d6a24e"
Manufacturer	"Chevorlet"
Device Type	"vehicle"
Active?	"Active"

---------------------------------------------------------------------------------------------------------------------------------------------
### ViewDeviceFiles
https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ViewDeviceFiles

Lists the file urls for devices with files. Parameters(sn=<Serial number of device to read files of>) full example of endpoint https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ViewDeviceFiles&sn=SN-000023a0d5d5c8c69b924fb815d6a56a
#### JSON output on successful read

Status	"Success"
MSG	"Successfully found file(s) for device SN-000023a0d5d5c8c69b924fb815d6a56a."
test1.pdf	"https://ec2-44-202-167-169.compute-1.amazonaws.com/files/5000007/test1.pdf"
test2.pdf	"https://ec2-44-202-167-169.compute-1.amazonaws.com/files/5000007/test2.pdf"
test3.pdf	"https://ec2-44-202-167-169.compute-1.amazonaws.com/files/5000007/test3.pdf"

---------------------------------------------------------------------------------------------------------------------------------------------
### UpdateDevice
https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?UpdateDevice

Updates device. Parameters(sn=<Serial number of device to update>,manu=<Manufacturer to update to>,type=<Type to update to>,active=<active or deactive>,newsn=<New serial number that device will have upon update>) sn parameter is required as well as at least 1 other parameter. full example of endpoint https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?UpdateDevice&sn=SN-000010c0d5d5c8c69b924fb706d6a24e&manu=Ford&active=deactive

#### JSON output on successful read
Status	"Success"
MSG	"Device successfully updated!"
Serial Number	"SN-000010c0d5d5c8c69b924fb706d6a24e"
Manufacturer	"Ford"
Device Type	"vehicle"
Active?	"Inactive"

---------------------------------------------------------------------------------------------------------------------------------------------
### UploadFile
https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?UploadFile

Upload file to a device. Parameters(sn=<Serial number of device to recieve file>,upload=<file to upload>) Did not get this one to work via PowerShell but was successful via Postman. Full example of endpoint https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?UploadFile&sn=SN-000010c0d5d5c8c69b924fb706d6a24e 

#### JSON output on successful read
{
    "Status": "Success",
    "MSG": "Successfully uploaded file test.pdf for device with serial number: SN-000010c0d5d5c8c69b924fb706d6a24e"
}

---------------------------------------------------------------------------------------------------------------------------------------------
### SearchDevices
https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices
Generic search for when wanting to browse via Type and Manufacturer. Parameters(type=<type of devices to return>,manu=<manufacturer of devices to return>) At least 1 parameter required to run. Full example of endpoint https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&type=computer&manu=HP

#### JSON output on successful read

Status	"Success"
MSG	"Successfully found devices that met criteria!"
Total	"64209"
SN-0000a9c6c522b1b6b6d4567649a70955	"https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ReadDevice&sn=SN-0000a9c6c522b1b6b6d4567649a70955"
SN-05eda68a9e9ac350b93127701daa26a2	"https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ReadDevice&sn=SN-05eda68a9e9ac350b93127701daa26a2"
SN-05eeb133ca2d16b4a6f7930bca14f120	"https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ReadDevice&sn=SN-05eeb133ca2d16b4a6f7930bca14f120"
SN-05eed85b453b54b72174dd6c58ed668a	"https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ReadDevice&sn=SN-05eed85b453b54b72174dd6c58ed668a"
SN-11d9c3ba44ec769fa71f7ada106632f7	"https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ReadDevice&sn=SN-11d9c3ba44ec769fa71f7ada106632f7"
SN-11d9d1c37298427b9147ab1723c7377b	"https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ReadDevice&sn=SN-11d9d1c37298427b9147ab1723c7377b"
SN-11d9ddb890dcad0abc4a1dfbb6fea0b1	"https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ReadDevice&sn=SN-11d9ddb890dcad0abc4a1dfbb6fea0b1"
SN-11da0daa39c621476acc6a53cc6c61f9	"https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ReadDevice&sn=SN-11da0daa39c621476acc6a53cc6c61f9"
SN-11db10e012198449067109bcd7b8abae	"https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ReadDevice&sn=SN-11db10e012198449067109bcd7b8abae"
SN-11db134985c3b59f3cd99ce495b3374e	"https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?ReadDevice&sn=SN-11db134985c3b59f3cd99ce495b3374e"
Next	"https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?SearchDevices&manu=HP&type=computer&pointer=459&direction=next"
Previous	"Beginning of Search"

---------------------------------------------------------------------------------------------------------------------------------------------
### DeleteDevice
https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?DeleteDevice

Delete device specified. Parameters(sn=<Serial number of device to delete>). Full example of endpoint https://ec2-44-202-167-169.compute-1.amazonaws.com/api/?DeleteDevice&sn=SN-fee5f6d1336b691267f1565328a526ca

#### JSON output on successful read

Status	"Success"
MSG	"Device with Serial Number: SN-fee5f6d1336b691267f1565328a526ca successfully deleted!"
