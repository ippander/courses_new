var myApp = angular.module('myApp', ['ng-admin']);

myApp.config(['NgAdminConfigurationProvider', function(NgAdminConfigurationProvider) {

    var nga = NgAdminConfigurationProvider;

    var courseEntity = nga.entity('course').label('Kurssi');

    courseEntity.creationView()
        .fields([
            nga.field('name'),
            nga.field('description')
        ])

    courseEntity.editionView()
        .fields(courseEntity.creationView().fields())

    courseEntity.showView()
        .fields(courseEntity.creationView().fields())

    courseEntity.listView()
        .title('Kurssit')
        .fields(courseEntity.creationView().fields())
        .listActions(['edit'])

    var placeEntity = nga.entity('place').label('Paikka')

    placeEntity.creationView()
        .fields([
            nga.field('name')
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
            nga.field('place_id', 'reference')
                .targetEntity(placeEntity)
                .targetField(nga.field('name'))
                .label('Paikka'),
            nga.field('start_time').label('Aloitusaika'),
            nga.field('end_time').label('Lopetusaika'),
            nga.field('regstartdate', 'datetime').label('Ilmoittautuminen alkaa'),
            nga.field('start_date', 'date').label('Tapahtuma alkaa'),
            nga.field('max_participants').label('Max osallistujamäärä'),
            nga.field('price').label('Hinta'),
            nga.field('member_price').label('Jäsenhinta')
        ])

    eventEntity.editionView()
        .fields(eventEntity.creationView().fields())

    eventEntity.showView()
        .fields(eventEntity.creationView().fields())

    eventEntity.listView()
        .title('Kausi')
        .fields(eventEntity.creationView().fields())
        .listActions(['edit'])

    // create an admin application
    var admin = nga.application('Aurajoen uinnin kurssien hallinta').baseApiUrl('/api/admin/')
        .addEntity(courseEntity)
        .addEntity(placeEntity)
        .addEntity(seasonEntity)
        .addEntity(eventEntity)

    // nga.dashboard();

    // more configuration here later
    // ...
    // attach the admin application to the DOM and run it
    nga.configure(admin);
}]);
