declare namespace App.DTOs {
export type TGame = {
id: number;
first_team_id: number;
second_team_id: number;
date: string;
location_id: number;
};
export type TLocation = {
id: number;
name: string;
};
export type TPlayer = {
id: number;
first_name: string;
last_name: string;
};
export type TTeam = {
id: number;
};
}
