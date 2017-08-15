
-- !!! VARASIJAT PUUTTUU !!!

update participant pa, (
	select pa.id participation_id,
		round(((10/100) * e.price), 2) discount,
		round(e.price - ((10/100) * e.price), 2) final_price
	from ( 
		-- accounts that have under aged siblings participating on courses same time
		select p.id, p.account_id
		from
			person p
			inner join participant pa on (p.id=pa.person_id)
			left join course_event e on (pa.event_id=e.id)
		where pa.reskontra is null and p.birthday > '1999-08-12'
		group by p.account_id
		having count(distinct(p.id)) > 1
		-- order by p.account_id asc, p.birthday desc
	) as s_count
		left join person p on (s_count.account_id=p.account_id)
		left join participant pa on (p.id=pa.person_id)
		left join course_event e on (pa.event_id=e.id)
	where pa.id is not null and p.birthday > '1999-08-12'
	order by p.account_id asc, p.birthday desc
) sib
set pa.discounted_amount=sib.discount, pa.discount_type = 1, pa.price = sib.final_price
where pa.id=sib.participation_id
;

update participant pa, course_event e
set pa.price=e.price
where pa.price is null and pa.event_id=e.id
;





select pe.first_name, pe.last_name, c.name, pl.name, e.weekday, e.start_time, e.end_time
from person pe, participant pa, product c, course_event e, place pl
where
	pe.id=pa.person_id
	and pa.event_id = e.id
	and e.product_id=c.id
	and e.place_id = pl.id
order by pe.id
;

select c.id, e.id, c.name, pl.name, e.notes, e.max_participants, count(*) lkm
from product c, course_event e, participant p, person pe, place pl
where c.id=e.product_id and e.place_id=pl.id and e.id=p.event_id and p.person_id=pe.id
group by c.id, e.id
order by lkm desc
;

SELECT p.id as course_id, p.name as course_name, p.description as course_description,
	e.id as event_id, e.start_date, e.end_date, e.weekday, e.start_time, e.end_time,
    e.price, e.member_price, e.notes, pl.name as place, pl.address, e.max_participants,
    count(*) as current_participants
FROM product p, course_event e, place pl, participant pa
WHERE p.id=e.product_id AND e.place_id=pl.id AND pa.event_id=e.id
GROUP BY p.id, e.id
ORDER BY p.name, e.id


SELECT p.id as course_id, p.name as course_name, p.description as course_description,
	e.id as event_id, e.start_date, e.end_date, e.weekday, e.start_time, e.end_time,
    e.price, e.member_price, e.notes, pl.name as place, pl.address, e.max_participants,
    count(*) as current_participants
FROM product p
	INNER JOIN course_event e ON (p.id=e.product_id)
    INNER JOIN place pl ON (e.place_id=pl.id)
    INNER JOIN participant pa on (pa.event_id=e.id)
GROUP BY p.id, e.id
ORDER BY p.name, e.id