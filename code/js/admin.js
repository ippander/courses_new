var myApp = angular.module('myApp', ['ng-admin']);

myApp.config(['NgAdminConfigurationProvider', function(NgAdminConfigurationProvider) {

    var nga = NgAdminConfigurationProvider;

    var enrollmentEntity = nga.entity('enrollment').label('Ilmoittautumiset')
    var accountEntity = nga.entity('account').label('Käyttäjätili')

    // accountEntity.creationView().fields([
    //     nga.field('email'),
    //     nga.field('password')
    //     ])

    accountEntity.listView()
        .title('Käyttäjätilit')
        .fields([nga.field('email', 'email')])
        .listActions(['edit'])
        .sortField('email')
        .sortDir('ASC')


    accountEntity.editionView().title('Käyttäjätili').fields([
        nga.field('email', 'email'),
        nga.field('password', 'password')
        ])

    var personEntity = nga.entity('person').label('Kurssilainen')

    personEntity.creationView()
        .fields([
            nga.field('first_name'),
            nga.field('last_name'),
            nga.field('birthday'),
            nga.field('notes', 'text')
        ])

    personEntity.editionView()
        .fields(personEntity.creationView().fields()
                .concat(
                    nga.field('enrollment', 'referenced_list').label('Osallistujat')
                        .targetEntity(enrollmentEntity)
                        .targetReferenceField('person_id')
                        // .targetReferenceField('event_id')
                        .targetFields([
                                nga.field('enrolled_at').label('Ilmoittautui'),
                                nga.field('course_name').label('Kurssi'),
                                nga.field('weekday', 'choice')
                                .label('Päivä')
                                .choices([
                                        { label: 'Maanantai', value: '0' },
                                        { label: 'Tiistai', value: '1' },
                                        { label: 'Keskiviikko', value: '2' },
                                        { label: 'Torstai', value: '3' },
                                        { label: 'Perjantai', value: '4' },
                                        { label: 'Lauantai', value: '5' },
                                        { label: 'Sunnuntai', value: '6' }
                                    ]),
                                nga.field('place').label('Paikka'),
                                nga.field('start_time'),
                                nga.field('end_time')
                            ])
                        .listActions(['delete'])
                    )
            )

    personEntity.showView()
        .fields(personEntity.creationView().fields())

    personEntity.listView()
        .title('Kurssilaiset')
        .fields(personEntity.creationView().fields())
        .listActions(['edit'])
        .sortField('last_name, first_name')
        .sortDir('ASC')





    var courseEntity = nga.entity('course').label('Kurssi');

    courseEntity.creationView()
        .fields([
            nga.field('name'),
            nga.field('description', 'text')
        ])

    courseEntity.editionView()
        .fields(courseEntity.creationView().fields())

    courseEntity.showView()
        .fields(courseEntity.creationView().fields())

    courseEntity.listView()
        .title('Kurssit')
        .fields(courseEntity.creationView().fields())
        .listActions(['edit'])
        .sortField('name')
        .sortDir('ASC')
        .filters([ nga.field('name') ])

    // courseEntity.filterView().fields([ nga.field('name') ])

    var placeEntity = nga.entity('place').label('Paikka')

    placeEntity.creationView()
        .fields([
            nga.field('name'),
            nga.field('address')
        ])

    placeEntity.editionView()
        .fields(placeEntity.creationView().fields())

    placeEntity.showView()
        .fields(placeEntity.creationView().fields())

    placeEntity.listView()
        .title('Suorituspaikat')
        .fields(placeEntity.creationView().fields())
        .listActions(['edit'])

    var seasonEntity = nga.entity('season').label('Kausi')

    seasonEntity.creationView()
        .fields([
            nga.field('name'),
            nga.field('season_start'),
            nga.field('season_end')
        ])

    seasonEntity.editionView()
        .fields(seasonEntity.creationView().fields())

    seasonEntity.showView()
        .fields(seasonEntity.creationView().fields())

    seasonEntity.listView()
        .title('Kausi')
        .fields(seasonEntity.creationView().fields())
        .listActions(['edit'])

    var eventEntity = nga.entity('event').label('Tapahtuma')

    eventEntity.creationView()
        .fields([
            nga.field('product_id', 'reference')
                .targetEntity(courseEntity)
                .targetField(nga.field('name'))
                .label('Kurssi'),
            nga.field('season_id', 'reference')
                .targetEntity(seasonEntity)
                .targetField(nga.field('name'))
                .label('Kausi'),
            nga.field('period', 'choice')
                .label('Jakso')
                .choices([
                        { label: 'Syksy 1. periodi', value: 'Q1' },
                        { label: 'Syksy 2. periodi', value: 'Q2' },
                        { label: 'Koko syksy', value: 'H1' },
                        { label: 'Kevät 1. periodi', value: 'Q3' },
                        { label: 'Kevät 2. periodi', value: 'Q4' },
                        { label: 'Koko kevät', value: 'H2' },
                        { label: 'Kesä', value: 'SUMMER' }
                    ]),
            nga.field('place_id', 'reference')
                .targetEntity(placeEntity)
                .targetField(nga.field('name'))
                .label('Paikka'),
            nga.field('weekday', 'choice')
                .label('Viikonpäivä')
                .choices([
                        { label: 'Maanantai', value: '0' },
                        { label: 'Tiistai', value: '1' },
                        { label: 'Keskiviikko', value: '2' },
                        { label: 'Torstai', value: '3' },
                        { label: 'Perjantai', value: '4' },
                        { label: 'Lauantai', value: '5' },
                        { label: 'Sunnuntai', value: '6' }
                    ]),
            nga.field('start_time').label('Aloitusaika'),
            nga.field('end_time').label('Lopetusaika'),
            nga.field('regstartdate', 'datetime').label('Ilmoittautuminen alkaa'),
            nga.field('start_date', 'date').label('Tapahtuma alkaa'),
            nga.field('end_date', 'date').label('Tapahtuma päättyy'),
            nga.field('notes', 'text').label('Huomioita').defaultValue(''),            
            nga.field('max_participants').label('Max osallistujamäärä'),
            nga.field('price', 'float').label('Hinta'),
            nga.field('member_price', 'float').label('Jäsenhinta')
        ])

    eventEntity.editionView()
        .fields(eventEntity.creationView().fields())

    eventEntity.showView()
        .fields([
            eventEntity.creationView().fields()
                .concat(
                    nga.field('enrollment', 'referenced_list').label('Osallistujat')
                        .targetEntity(enrollmentEntity)
                        .targetReferenceField('event_id')
                        // .targetReferenceField('event_id')
                        .targetFields([
                                nga.field('first_name').label('Etunimi'),
                                nga.field('last_name').label('Sukunimi'),
                                nga.field('birthday').label('Syntymäaika'),
                                nga.field('enrolled_at').label('Ilmoittautui')
                            ])
                        .listActions(['delete'])
                    )
            ])
        // .fields([
        //     nga.field('participants', 'referenced_list')
        //         .targetEntity('enrollmentEntity')
        //         .targetReferenceField('id')
        //         // .targetFields([
        //         //     nga.field('person_id')
        //         //     ])
        //     ])

    eventEntity.listView()
        .title('Tapahtumat')
        .fields(
            nga.field('course_name').label('Kurssi'),
                // .targetEntity(courseEntity)
                // .targetField(nga.field('name'))
                // .label('Kurssi'),
            nga.field('season_name').label('Kausi'),
            nga.field('period', 'choice')
                .label('Jakso')
                .choices([
                        { label: 'Syksy 1. periodi', value: 'Q1' },
                        { label: 'Syksy 2. periodi', value: 'Q2' },
                        { label: 'Koko syksy', value: 'H1' },
                        { label: 'Kevät 1. periodi', value: 'Q3' },
                        { label: 'Kevät 2. periodi', value: 'Q4' },
                        { label: 'Koko kevät', value: 'H2' },
                        { label: 'Kesä', value: 'SUMMER' }
                    ]),
            nga.field('place_name').label('Paikka'),
            nga.field('weekday', 'choice')
                .label('Viikonpäivä')
                .choices([
                        { label: 'Maanantai', value: '0' },
                        { label: 'Tiistai', value: '1' },
                        { label: 'Keskiviikko', value: '2' },
                        { label: 'Torstai', value: '3' },
                        { label: 'Perjantai', value: '4' },
                        { label: 'Lauantai', value: '5' },
                        { label: 'Sunnuntai', value: '6' }
                    ]),
            nga.field('start_time').label('Aloitusaika'),
            nga.field('end_time').label('Lopetusaika')

            )
        .sortField('course_name, weekday, start_time')
        .sortDir('ASC')
        .listActions(['show', 'edit'])

    // create an admin application
    // var admin = nga.application('Aurajoen uinnin kurssien hallinta').baseApiUrl('/courses/courses_new/api/admin/')
    var admin = nga.application('Aurajoen uinnin kurssien hallinta').baseApiUrl('/api/admin/')
        .addEntity(courseEntity)
        .addEntity(placeEntity)
        .addEntity(seasonEntity)
        .addEntity(eventEntity)
        .addEntity(enrollmentEntity)
        .addEntity(personEntity)
        .addEntity(accountEntity)

    // nga.dashboard();

    // more configuration here later
    // ...
    // attach the admin application to the DOM and run it
    nga.configure(admin);
}]);

// myApp.config(['RestangularProvider', function(RestangularProvider) {
//     RestangularProvider.setDefaultHttpFields({cache: true})
// }]);