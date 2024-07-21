<?php

    namespace App\Http\Controllers;

    use Illuminate\Http\Request;

    class BundlePlansController extends Controller
    {

        function DataPlans () {
            
            $data = [

                "MTN" => [
                    [
                        "ID" => "01",
                        "PRODUCT" => [

                            [
                                "PRODUCT_CODE" => "1",
                                "PRODUCT_ID" => "260",
                                "PRODUCT_NAME" => "150 MB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "50"
                            ],


                            [
                                "PRODUCT_CODE" => "2",
                                "PRODUCT_ID" => "49",
                                "PRODUCT_NAME" => "250 MB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "72.5"
                            ],

                            // [
                            //     "PRODUCT_CODE" => "34",
                            //     "PRODUCT_ID" => "274",
                            //     "PRODUCT_NAME" => "500 MB - 30 days (Transfer)",
                            //     "PRODUCT_AMOUNT" => "112.5"
                            // ],


                            // [
                            //     "PRODUCT_CODE" => "35",
                            //     "PRODUCT_ID" => "254",
                            //     "PRODUCT_NAME" => "1 GB - 30 days (Transfer)",
                            //     "PRODUCT_AMOUNT" => "225"
                            // ],


                            [
                                "PRODUCT_CODE" => "40",
                                "PRODUCT_ID" => "215",
                                "PRODUCT_NAME" => "1 GB - 1 day (Gifting)",
                                "PRODUCT_AMOUNT" => "205"
                            ],

                            // [
                            //     "PRODUCT_CODE" => "36",
                            //     "PRODUCT_ID" => "253",
                            //     "PRODUCT_NAME" => "2 GB - 30 days (Transfer)",
                            //     "PRODUCT_AMOUNT" => "450"
                            // ],

                            // [
                            //     "PRODUCT_CODE" => "37",
                            //     "PRODUCT_ID" => "257",
                            //     "PRODUCT_NAME" => "3 GB - 30 days (Transfer)",
                            //     "PRODUCT_AMOUNT" => "675"
                            // ],


                            [
                                "PRODUCT_CODE" => "41",
                                "PRODUCT_ID" => "216",
                                "PRODUCT_NAME" => "3.5 GB - 2 days (Gifting)",
                                "PRODUCT_AMOUNT" => "510"
                            ],

                            [
                                "PRODUCT_CODE" => "42",
                                "PRODUCT_ID" => "217",
                                "PRODUCT_NAME" => "15 GB - 7 days (Gifting)",
                                "PRODUCT_AMOUNT" => "2030.0"
                            ],

                            // [
                            //     "PRODUCT_CODE" => "38",
                            //     "PRODUCT_ID" => "256",
                            //     "PRODUCT_NAME" => "5 GB - 30 days (Transfer)",
                            //     "PRODUCT_AMOUNT" => "1125"
                            // ],

                            // [
                            //     "PRODUCT_CODE" => "39",
                            //     "PRODUCT_ID" => "275",
                            //     "PRODUCT_NAME" => "10 GB - 30 days (Transfer)",
                            //     "PRODUCT_AMOUNT" => "2250"
                            // ],

                            [
                                "PRODUCT_CODE" => "3",
                                "PRODUCT_ID" => "214",
                                "PRODUCT_NAME" => "500 MB - 30 days (SME)",
                                "PRODUCT_AMOUNT" => "129"
                            ],

                            [
                                "PRODUCT_CODE" => "4",
                                "PRODUCT_ID" => "259",
                                "PRODUCT_NAME" => "500 MB - 30 days (SME2)",
                                "PRODUCT_AMOUNT" => "126"
                            ],


                            [
                                "PRODUCT_CODE" => "5",
                                "PRODUCT_ID" => "212",
                                "PRODUCT_NAME" => "500 MB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "130"
                            ],

                            [
                                "PRODUCT_CODE" => "7",
                                "PRODUCT_ID" => "7",
                                "PRODUCT_NAME" => "1.0 GB - 30 days (SME)",
                                "PRODUCT_AMOUNT" => "258"
                            ],

                            [
                                "PRODUCT_CODE" => "13",
                                "PRODUCT_ID" => "8",
                                "PRODUCT_NAME" => "2.0 GB - 30 days (SME)",
                                "PRODUCT_AMOUNT" => "516"
                            ],

                            [
                                "PRODUCT_CODE" => "16",
                                "PRODUCT_ID" => "233",
                                "PRODUCT_NAME" => "2.0 GB - 30 days (SME2)",
                                "PRODUCT_AMOUNT" => "504"
                            ],

                            [
                                "PRODUCT_CODE" => "17",
                                "PRODUCT_ID" => "209",
                                "PRODUCT_NAME" => "2 GB - 30 days (COPORATE)",
                                "PRODUCT_AMOUNT" => "520"
                            ],


                            [
                                "PRODUCT_CODE" => "19",
                                "PRODUCT_ID" => "44",
                                "PRODUCT_NAME" => "3.0 GB - 30 days (SME)",
                                "PRODUCT_AMOUNT" => "774"
                            ],

                            [
                                "PRODUCT_CODE" => "20",
                                "PRODUCT_ID" => "234",
                                "PRODUCT_NAME" => "3.0 GB - 30 days (SME2)",
                                "PRODUCT_AMOUNT" => "756"
                            ],


                            [
                                "PRODUCT_CODE" => "22",
                                "PRODUCT_ID" => "210",
                                "PRODUCT_NAME" => "3 GB - 30 days (COPORATE)",
                                "PRODUCT_AMOUNT" => "780"
                            ],


                            [
                                "PRODUCT_CODE" => "23",
                                "PRODUCT_ID" => "11",
                                "PRODUCT_NAME" => "5.0 GB - 30 days (SME)",
                                "PRODUCT_AMOUNT" => "1290"
                            ],

                            [
                                "PRODUCT_CODE" => "24",
                                "PRODUCT_ID" => "235",
                                "PRODUCT_NAME" => "5.0 GB - 30 days (SME2)",
                                "PRODUCT_AMOUNT" => "1260"
                            ],

                            [
                                "PRODUCT_CODE" => "26",
                                "PRODUCT_ID" => "211",
                                "PRODUCT_NAME" => "5.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "1300"
                            ],

                            [
                                "PRODUCT_CODE" => "27",
                                "PRODUCT_ID" => "213",
                                "PRODUCT_NAME" => "10.0 GB - 30 days (SME)",
                                "PRODUCT_AMOUNT" => "2580"
                            ],


                            [
                                "PRODUCT_CODE" => "28",
                                "PRODUCT_ID" => "236",
                                "PRODUCT_NAME" => "10.0 GB - 30 days (SME2)",
                                "PRODUCT_AMOUNT" => "2520"
                            ],

                            [
                                "PRODUCT_CODE" => "30",
                                "PRODUCT_ID" => "43",
                                "PRODUCT_NAME" => "10.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "2600"
                            ],

                            [
                                "PRODUCT_CODE" => "31",
                                "PRODUCT_ID" => "223",
                                "PRODUCT_NAME" => "15.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "3900"
                            ],

                            [
                                "PRODUCT_CODE" => "32",
                                "PRODUCT_ID" => "222",
                                "PRODUCT_NAME" => "20.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "5200"
                            ],

                            [
                                "PRODUCT_CODE" => "33",
                                "PRODUCT_ID" => "237",
                                "PRODUCT_NAME" => "40.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "10320"
                            ]


                        ]
                    ]
                ],

                "Glo" => [
                    [
                        "ID" => "02",
                        "PRODUCT" => [

                            [
                                "PRODUCT_CODE" => "1",
                                "PRODUCT_ID" => "225",
                                "PRODUCT_NAME" => "200 MB - 14 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "54.5"
                            ],

                            [
                                "PRODUCT_CODE" => "2",
                                "PRODUCT_ID" => "203",
                                "PRODUCT_NAME" => "500.0 MB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "119.5"
                            ],

                            [
                                "PRODUCT_CODE" => "3",
                                "PRODUCT_ID" => "194",
                                "PRODUCT_NAME" => "1.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "239"
                            ],

                            [
                                "PRODUCT_CODE" => "4",
                                "PRODUCT_ID" => "195",
                                "PRODUCT_NAME" => "2.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "478"
                            ],

                            [
                                "PRODUCT_CODE" => "5",
                                "PRODUCT_ID" => "196",
                                "PRODUCT_NAME" => "3.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "717"
                            ],

                            [
                                "PRODUCT_CODE" => "6",
                                "PRODUCT_ID" => "197",
                                "PRODUCT_NAME" => "5.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "1195"
                            ],

                            [
                                "PRODUCT_CODE" => "7",
                                "PRODUCT_ID" => "200",
                                "PRODUCT_NAME" => "10.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "2390"
                            ],

                            // [
                            //     "PRODUCT_CODE" => "8",
                            //     "PRODUCT_ID" => "261",
                            //     "PRODUCT_NAME" => "300.0 GB - 30 days (CORPORATE)",
                            //     "PRODUCT_AMOUNT" => "60000"
                            // ],

                            // [
                            //     "PRODUCT_CODE" => "9",
                            //     "PRODUCT_ID" => "262",
                            //     "PRODUCT_NAME" => "500.0 GB - 30 days (CORPORATE)",
                            //     "PRODUCT_AMOUNT" => "100000"
                            // ]

                            // [
                            //     "PRODUCT_CODE" => "10",
                            //     "PRODUCT_ID" => "263",
                            //     "PRODUCT_NAME" => "1.0 TB - 30 days (CORPORATE)",
                            //     "PRODUCT_AMOUNT" => "200000"
                            // ]


                        ]
                    ]
                ],

                "m_9mobile" => [
                    [
                        "ID" => "03",
                        "PRODUCT" => [

                            [
                                "PRODUCT_CODE" => "7",
                                "PRODUCT_ID" => "221",
                                "PRODUCT_NAME" => "500.0 MB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "80"
                            ],

                            [
                                "PRODUCT_CODE" => "1",
                                "PRODUCT_ID" => "183",
                                "PRODUCT_NAME" => "1.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "150"
                            ],

                            [
                                "PRODUCT_CODE" => "2",
                                "PRODUCT_ID" => "184",
                                "PRODUCT_NAME" => "1.5 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "227"
                            ],

                            [
                                "PRODUCT_CODE" => "3",
                                "PRODUCT_ID" => "185",
                                "PRODUCT_NAME" => "2 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "300"
                            ],

                            [
                                "PRODUCT_CODE" => "4",
                                "PRODUCT_ID" => "186",
                                "PRODUCT_NAME" => "3 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "450"
                            ],

                            [
                                "PRODUCT_CODE" => "6",
                                "PRODUCT_ID" => "188",
                                "PRODUCT_NAME" => "5 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "750"
                            ],

                            [
                                "PRODUCT_CODE" => "5",
                                "PRODUCT_ID" => "189",
                                "PRODUCT_NAME" => "10 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "1500"
                            ],

                            [
                                "PRODUCT_CODE" => "7",
                                "PRODUCT_ID" => "229",
                                "PRODUCT_NAME" => "20.0 GB - Monthly (CORPORATE)",
                                "PRODUCT_AMOUNT" => "3000"
                            ]

                        ]
                    ]
                ],

                "Airtel" => [
                    [
                        "ID" => "04",
                        "PRODUCT" => [

                            [
                                "PRODUCT_CODE" => "1",
                                "PRODUCT_ID" => "149",
                                "PRODUCT_NAME" => "100.0 MB - 14 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "55"
                            ],

                            [
                                "PRODUCT_CODE" => "2",
                                "PRODUCT_ID" => "193",
                                "PRODUCT_NAME" => "300.0 MB - 14 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "105"
                            ],

                            [
                                "PRODUCT_CODE" => "3",
                                "PRODUCT_ID" => "165",
                                "PRODUCT_NAME" => "500.0 MB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "136"
                            ],

                            [
                                "PRODUCT_CODE" => "4",
                                "PRODUCT_ID" => "145",
                                "PRODUCT_NAME" => "1.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "272"
                            ],

                            [
                                "PRODUCT_CODE" => "5",
                                "PRODUCT_ID" => "146",
                                "PRODUCT_NAME" => "2.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "544"
                            ],
                            [
                                "PRODUCT_CODE" => "6",
                                "PRODUCT_ID" => "147",
                                "PRODUCT_NAME" => "5.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "1360"
                            ],

                            [
                                "PRODUCT_CODE" => "8",
                                "PRODUCT_ID" => "148",
                                "PRODUCT_NAME" => "10.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "2720"
                            ],

                            [
                                "PRODUCT_CODE" => "7",
                                "PRODUCT_ID" => "226",
                                "PRODUCT_NAME" => "15.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "4080"
                            ],

                            [
                                "PRODUCT_CODE" => "9",
                                "PRODUCT_ID" => "227",
                                "PRODUCT_NAME" => "20.0 GB - 30 days (CORPORATE)",
                                "PRODUCT_AMOUNT" => "5440.0"
                            ]


                        ]
                    ]
                ]

            ];
            return $data;
        }

        // Data Plan for User
        public function DataPlansUser () {

            $data = $this->DataPlans();

            // Adding 4 percent to all product amounts
            foreach ($data as &$network) {
                foreach ($network as &$networkDetails) {
                    foreach ($networkDetails['PRODUCT'] as &$product) {
                        $product['PRODUCT_AMOUNT'] = ($product['PRODUCT_AMOUNT'] * env("DATA_USER")) + $product['PRODUCT_AMOUNT'] ;
                    }
                }
            }

            return $data;


        }

        // Data Plan for Api
        public function DataPlansUserApi () {

           $data = $this->DataPlans();

            // Adding 4 percent to all product amounts
            foreach ($data as &$network) {
                foreach ($network as &$networkDetails) {
                    foreach ($networkDetails['PRODUCT'] as &$product) {
                        $product['PRODUCT_AMOUNT'] = ($product['PRODUCT_AMOUNT'] * env("DATA_API")) + $product['PRODUCT_AMOUNT'];
                    }
                }
            }

            return $data;

        }

    }
