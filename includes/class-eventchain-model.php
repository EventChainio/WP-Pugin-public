<?php

class Eventchain_Model
{
    /**

    CREATE TABLE `wphandzon`.`new_table` (
  `idnew_table` INT NOT NULL,
  `EventID` INT NOT NULL,
  `Content` LONGTEXT NOT NULL,
  PRIMARY KEY (`idnew_table`),
  UNIQUE INDEX `idnew_table_UNIQUE` (`idnew_table` ASC),
  UNIQUE INDEX `EventID_UNIQUE` (`EventID` ASC));

     * Returns the events database results by a given email address
     * @param $userEmail
     * @return array
     */
    public static function events_by_email($userEmail)
    {
        //GRAPHQL request
        $dateFrom = date('Y-m-d', strtotime('-1years'));
        $query = '
        {
            getEvents(SearchParams: { VendorEmail: "'.$userEmail.'", DateFrom: "'.$dateFrom.'", Count: 30 }) {
                ID
                VendorID
                EventInfo {
                    Title
                    OrganizerName
                    OrganizerStatus
                    Host
                    StartDate
                    EndDate
                    EventImageURL
                }
                EventLocation {
                    VenueName
                }
            }
        }';

        $response = self::getResponse($query);

        $responseJSON = (false === empty($response['body'])) ? $response['body'] : '';
        $arResponse = json_decode($responseJSON, true);

        return (false === empty($arResponse['data']['getEvents']) && true === is_array($arResponse['data']['getEvents']))
            ? array_map('self::flattenResults', $arResponse['data']['getEvents']) : [];
    }

    /**
     * Returns the event details by a given ID
     * @param int $id
     * @return array|null
     */
    public static function event_by_id($id) {
        if (false === is_int($id)) {
            throw new InvalidArgumentException('Argument $id must be an integer');
        }

        $query = '
        {
            getEventDetails(EventID: '.$id.') {
                event {
                    EventInfo {
                        Title
                        OrganizerName
                        Host
                        StartDate
                        EndDate
                        Description
                        EventImageURL
                        TimeZone {
                            TZoneID
                            FullName
                            Name
                        }
                    }
                    EventLocation {
                        VenueID
                        VenueName
                        DisplayMapOnEvent
                        Address {
                            Address1
                            Address2
                            City
                            CountryID
                            CountryName
                            ProvinceID
                            ProvinceName
                            ProvinceShortName
                            PostalCode
                            MapCoords {
                                Lat
                                Long
                                Zoom
                            }
                        }
                    }
                    TicketTypes {
                        TicketTypeID
                        TypeName
                        EventID
                        Category
                        SeatingType
                        Price
                        Quantity
                        Description
                        Available
                        Currency
                        CurrencySign
                    }
                    ApplicableTaxes {
                        EventTaxID
                        TaxName
                        TaxPercent
                    }
                }
            }
        }';

        $response = self::getResponse($query);
        $responseJSON = (false === empty($response['body'])) ? $response['body'] : '';

        return json_decode($responseJSON, true);
    }


    public static function getResponse($query) {
        return wp_remote_post('https://api.eventchain.io/open', array(
            'headers' => array(
                'Content-type' => 'application/json'
            ),
            'body' => json_encode(array(
                                      'query' => $query
                                  )),
            'method' => 'POST',
            'data_format' => 'body'
        ));
    }

    public static function flattenResults(array $result)
    {
        $startDate = new DateTime($result['EventInfo']['StartDate']);
        $endDate = new DateTime($result['EventInfo']['EndDate']);
        return array(
            'id' => $result['ID'],
            'title' => $result['EventInfo']['Title'],
            'startdate' => $startDate->format('Y-m-d g:i A'),
            'enddate' => $endDate->format('Y-m-d g:i A')
        );
    }
}