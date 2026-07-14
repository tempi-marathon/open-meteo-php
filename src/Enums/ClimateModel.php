<?php

/** @pest-mutate-ignore */

declare(strict_types=1);

namespace TempiMarathon\OpenMeteo\Enums;

enum ClimateModel: string
{
    case CMCCCM2VHR4 = 'CMCC_CM2_VHR4';
    case FGOALSF3H = 'FGOALS_f3_H';
    case HiRAMSITHR = 'HiRAM_SIT_HR';
    case MRIAGCM32S = 'MRI_AGCM3_2_S';
    case ECEarth3PHR = 'EC_Earth3P_HR';
    case MPIESM12XR = 'MPI_ESM1_2_XR';
    case NICAM168S = 'NICAM16_8S';
}
