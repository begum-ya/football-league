export interface ApiResponse<T> {
    success: boolean;
    data: T;
    message?: string;
  }
export type TeamStats = {
    team_id: number;
    played: number;
    won: number;
    draw: number;
    lose: number;
    points: number;
    goal_difference: number;
    team: { name: string };
}
export type Game = {
    home_team: { name: string, };
    away_team: { name: string, };
    away_score:number;
    home_score:number;
}
export type Fixture = {
    id: number;
    week: number;
    games: Game[];
}

export type Prediction = {
    team: string;
    championship_probability: string;
    message:string;
}
export type Team = {
    id: number;
    name: string;
}
export type Week = {
    current_week: number;
    week_title: string;
}
